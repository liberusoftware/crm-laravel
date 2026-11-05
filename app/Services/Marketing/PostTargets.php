<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Enums\SocialPlatform;
use App\Models\Team;
use App\Services\Zernio\ZernioTenantService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class PostTargets
{
    public function __construct(private readonly ZernioTenantService $tenants, private readonly PlatformConfiguration $configuration) {}

    public function options(Team $team): array
    {
        if ($this->configuration->provider() === 'direct') {
            return array_intersect_key(SocialPlatform::options(), array_flip(DirectPostPublisher::PLATFORMS));
        }
        $options = [];
        try {
            foreach ($this->accounts($team) as $account) {
                if (isset($account['platform'], $account['_id']) && in_array($account['platform'], ZernioTenantService::PLATFORMS, true)) {
                    $options[$account['platform'].'::'.$account['_id']] = ucfirst($account['platform']).' — '.($account['displayName'] ?? $account['username'] ?? $account['_id']);
                }
            }
        } catch (\Throwable $exception) {
            report($exception);
        }

        return ['Connected accounts' => $options, 'Channels for draft planning' => SocialPlatform::options()];
    }

    public function normalize(Team $team, array $targets, string $provider): array
    {
        $normalized = [];
        $owned = null;
        foreach ($targets as $target) {
            if (is_string($target) && str_contains($target, '::')) {
                [$platform, $id] = explode('::', $target, 2);
                $target = ['platform' => $platform, 'account_id' => $id];
            }
            if ($provider === 'zernio' && is_array($target)) {
                $data = Validator::make($target, ['platform' => ['required', Rule::in(ZernioTenantService::PLATFORMS)], 'account_id' => ['required', 'string', 'max:255']])->validate();
                $owned ??= $this->accounts($team);
                if (! collect($owned)->contains(fn ($account) => ($account['platform'] ?? null) === $data['platform'] && ($account['_id'] ?? null) === $data['account_id'])) {
                    throw ValidationException::withMessages(['platforms' => 'Choose a connected account in this workspace.']);
                }
                $normalized[] = $data;
            } else {
                $data = Validator::make(['platform' => $target], ['platform' => ['required', 'string', Rule::in($provider === 'zernio' ? ZernioTenantService::PLATFORMS : DirectPostPublisher::PLATFORMS)]])->validate();
                $normalized[] = $data['platform'];
            }
        }

        return $normalized;
    }

    private function accounts(Team $team): array
    {
        $response = $this->tenants->accounts($team);
        $accounts = data_get($response, 'accounts', data_get($response, 'data.accounts', []));

        return is_array($accounts) ? array_values(array_filter($accounts, 'is_array')) : [];
    }
}
