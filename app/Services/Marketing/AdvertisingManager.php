<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Models\Campaign;
use App\Models\Team;
use App\Models\User;
use App\Services\Zernio\ZernioClient;
use App\Services\Zernio\ZernioTenantService;
use App\Support\AccessContext;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class AdvertisingManager
{
    public function __construct(private readonly PlatformConfiguration $configuration, private readonly WorkspaceAccess $access, private readonly ZernioClient $zernio, private readonly ZernioTenantService $tenants) {}

    public static function platformKey(string $platform): string
    {
        return match ($platform) {
            'Facebook Advertising' => 'facebook', 'LinkedIn Business' => 'linkedin', 'Google AdWords' => 'google',
            default => strtolower($platform),
        };
    }

    public function execute(User $actor, Team $team, Campaign $campaign, string $operation): array
    {
        $this->access->authorize($actor, $team, 'update_campaign');
        abort_if(AccessContext::shouldMaskFields(), 403);
        abort_unless((int) $campaign->getAttribute('team_id') === (int) $team->getKey(), 404);
        abort_unless(in_array($operation, ['create', 'update', 'activate', 'pause', 'archive', 'analytics'], true), 422);
        $account = $campaign->advertisingAccount;
        abort_unless($account && (int) $account->team_id === (int) $team->getKey(), 422, 'Select an advertising account in this workspace.');
        $provider = data_get($campaign->metadata, 'management_provider', $this->configuration->provider());
        $lock = Cache::lock('marketing-campaign-'.$campaign->getKey(), 60);
        abort_unless($lock->get(), 409, 'Another campaign operation is in progress.');
        $claimed = false;
        try {
            $campaign->refresh();
            if ($operation === 'create') {
                DB::transaction(function () use ($campaign, $provider): void {
                    $locked = Campaign::query()->whereKey($campaign->getKey())->lockForUpdate()->firstOrFail();
                    abort_if(filled($locked->external_id) || data_get($locked->metadata, 'creation_pending'), 409, 'Campaign already submitted. Reconcile the provider before retrying.');
                    $locked->update(['metadata' => array_replace($locked->metadata ?? [], ['management_provider' => $provider, 'creation_pending' => true])]);
                });
                $claimed = true;
                $campaign->refresh();
            } else {
                abort_unless(filled($campaign->external_id), 422, 'Create the provider campaign first.');
            }
            $result = $provider === 'zernio' ? $this->throughZernio($team, $campaign, $operation) : $this->throughMeta($campaign, $operation);
            if ($operation === 'create') {
                $id = $result['campaignId'] ?? $result['id'] ?? null;
                if (! is_string($id) || $id === '') {
                    throw new RuntimeException('The provider did not return a campaign identifier. Reconcile before retrying.');
                }
                $campaign->update(['external_id' => $id, 'status' => 'paused', 'metadata' => array_replace($campaign->metadata ?? [], ['creation_pending' => false])]);
            } elseif (in_array($operation, ['activate', 'pause', 'archive'], true)) {
                $campaign->update(['status' => match ($operation) {
                    'activate' => 'active', 'pause' => 'paused', default => 'archived'
                }]);
            }

            return $result;
        } catch (\Throwable $exception) {
            if ($claimed && $operation === 'create' && (($exception instanceof HttpException && $exception->getStatusCode() >= 400 && $exception->getStatusCode() < 500) || ($exception->getCode() >= 400 && $exception->getCode() < 500))) {
                $campaign->update(['metadata' => array_replace($campaign->metadata ?? [], ['creation_pending' => false])]);
            }
            throw $exception;
        } finally {
            $lock->release();
        }
    }

    private function throughZernio(Team $team, Campaign $campaign, string $operation): array
    {
        $account = $campaign->advertisingAccount;
        $platform = self::platformKey($account->platform);
        $connectionId = data_get($account->metadata, 'zernio_account_id');
        $accounts = $this->tenants->accounts($team);
        $owned = collect(data_get($accounts, 'accounts', data_get($accounts, 'data.accounts', [])))->contains(fn ($row) => ($row['_id'] ?? null) === $connectionId);
        abort_unless($owned && is_string($connectionId), 403, 'Connect this advertising account through the workspace Zernio profile.');
        $profileId = $this->tenants->ensureProfile($team);
        if ($operation !== 'create') {
            $remote = $this->zernio->listCampaigns(['profileId' => $profileId, 'accountId' => $connectionId, 'adAccountId' => $account->account_id, 'campaignId' => $campaign->external_id, 'limit' => 100]);
            $campaigns = data_get($remote, 'campaigns', data_get($remote, 'data.campaigns', []));
            abort_unless(collect($campaigns)->contains(fn ($row) => (string) ($row['platformCampaignId'] ?? $row['campaignId'] ?? '') === (string) $campaign->external_id), 403, 'The remote campaign does not belong to this workspace account.');
        }
        $scope = ['platform' => $platform, 'accountId' => $connectionId];

        return match ($operation) {
            'create' => $this->zernio->createCampaign([
                'accountId' => $connectionId, 'adAccountId' => $account->account_id, 'name' => $campaign->name,
                'goal' => match ($campaign->objective) {
                    'awareness' => 'awareness', 'conversion' => 'conversions', default => 'traffic'
                },
                ...($platform === 'linkedin' ? [] : ['budgetAmount' => (float) $campaign->budget, 'budgetType' => $campaign->budget_type]), 'status' => 'PAUSED',
            ], 'crm-campaign-'.$team->getKey().'-'.$campaign->getKey()),
            'update' => $this->zernio->updateCampaign($campaign->external_id, $scope + (in_array($platform, ['facebook', 'instagram', 'reddit'], true) ? ['name' => $campaign->name] : []) + ['budget' => ['amount' => (float) $campaign->budget, 'type' => $campaign->budget_type]]),
            'activate', 'pause' => $this->zernio->campaignStatus($campaign->external_id, $scope + ['status' => $operation === 'activate' ? 'active' : 'paused']),
            'archive' => $this->zernio->deleteCampaign($campaign->external_id, $scope),
            'analytics' => $this->zernio->getCampaignAnalytics($campaign->external_id, ['profileId' => $profileId, 'accountId' => $connectionId]),
        };
    }

    private function throughMeta(Campaign $campaign, string $operation): array
    {
        abort_unless((bool) config('services.zernio.direct_api_enabled', true), 503, 'Standalone API access is disabled by the platform operator.');
        $account = $campaign->advertisingAccount;
        $platform = self::platformKey($account->platform);
        abort_unless(in_array($platform, ['facebook', 'instagram'], true), 422, 'Standalone campaign management currently supports Meta accounts. Use Zernio for other ad networks.');
        abort_unless(filled($account->access_token), 422, 'Reconnect the advertising account.');
        $version = (string) $this->configuration->credential('meta_api_version', config('services.facebook.graph_version'));
        abort_unless(preg_match('/^v[0-9]+\.[0-9]+$/', $version), 422, 'Configure the Meta Graph API version.');
        $request = Http::withToken($account->access_token)->acceptJson()->timeout(30);
        $base = 'https://graph.facebook.com/'.$version.'/';
        $id = rawurlencode((string) $campaign->external_id);
        $accountId = str_starts_with($account->account_id, 'act_') ? $account->account_id : 'act_'.$account->account_id;
        if ($operation !== 'create') {
            $ownership = $request->get($base.$id, ['fields' => 'account_id']);
            abort_unless($ownership->successful() && (string) $ownership->json('account_id') === str_replace('act_', '', $accountId), 403, 'Campaign does not belong to this advertising account.');
        }
        $exponent = (int) data_get($account->metadata, 'currency_exponent', 2);
        abort_unless($exponent >= 0 && $exponent <= 3 && (float) $campaign->budget > 0, 422, 'A positive budget and valid currency precision are required.');
        $budget = [$campaign->budget_type === 'lifetime' ? 'lifetime_budget' : 'daily_budget' => (int) round((float) $campaign->budget * (10 ** $exponent))];
        $response = match ($operation) {
            'create' => $request->asForm()->post($base.rawurlencode($accountId).'/campaigns', $budget + ['name' => $campaign->name, 'objective' => match ($campaign->objective) {
                'awareness' => 'OUTCOME_AWARENESS', 'conversion' => 'OUTCOME_SALES', default => 'OUTCOME_TRAFFIC'
            }, 'status' => 'PAUSED', 'special_ad_categories' => json_encode(data_get($campaign->metadata, 'special_ad_categories', []), JSON_THROW_ON_ERROR)]),
            'update' => $request->asForm()->post($base.$id, $budget + ['name' => $campaign->name]),
            'activate', 'pause', 'archive' => $request->asForm()->post($base.$id, ['status' => match ($operation) {
                'activate' => 'ACTIVE', 'pause' => 'PAUSED', default => 'ARCHIVED'
            }]),
            'analytics' => $request->get($base.$id.'/insights', ['fields' => 'impressions,clicks,spend,reach,cpc,cpm,ctr']),
        };
        if (! $response->successful()) {
            throw new RuntimeException('Meta advertising request failed (HTTP '.$response->status().'). Check account permissions and campaign settings.', $response->status());
        }

        return (array) $response->json();
    }
}
