<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\OAuthConfiguration;
use App\Models\Team;
use App\Models\User;
use App\Services\Marketing\DirectPostPublisher;
use App\Services\Marketing\PlatformConfiguration;
use App\Services\Marketing\WorkspaceAccess;
use App\Services\Zernio\ZernioTenantService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class MarketingConnections extends Page
{
    protected static ?string $title = 'Marketing connections';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected string $view = 'filament.app.pages.marketing-connections';

    public array $accounts = [];

    public string $provider = '';

    public string $platform = 'facebook';

    public array $destinations = [];

    public static function canAccess(): bool
    {
        $team = Filament::getTenant();
        $user = Filament::auth()->user();

        return $user instanceof User && $team instanceof Team && app(WorkspaceAccess::class)->canManage($user, $team);
    }

    public function mount(): void
    {
        abort_unless(self::canAccess(), 403);
        $this->reloadAccounts();
    }

    public function reloadAccounts(): void
    {
        abort_unless(self::canAccess(), 403);
        $this->provider = app(PlatformConfiguration::class)->provider();
        $team = Filament::getTenant();
        try {
            if ($this->provider === 'zernio') {
                $result = app(ZernioTenantService::class)->accounts($team);
                $accounts = data_get($result, 'accounts', data_get($result, 'data.accounts', []));
                $this->accounts = array_map(fn ($account) => ['id' => $account['_id'] ?? '', 'platform' => $account['platform'] ?? '', 'name' => $account['displayName'] ?? $account['username'] ?? $account['platform'] ?? 'Account', 'status' => ($account['isActive'] ?? true) ? 'Connected' : 'Reconnect required'], is_array($accounts) ? $accounts : []);
            } else {
                $this->accounts = OAuthConfiguration::query()->where('team_id', $team->getKey())->whereIn('service_name', DirectPostPublisher::PLATFORMS)->get()->map(function ($account) {
                    $this->destinations[$account->id] = data_get($account->additional_settings, 'publishing_id', '');

                    return ['id' => $account->id, 'platform' => $account->service_name, 'name' => $account->account_name ?? $account->service_name, 'status' => $account->is_active && $account->access_token ? 'Connected' : 'Reconnect required'];
                })->all();
            }
        } catch (\Throwable $exception) {
            report($exception);
            Notification::make()->title('Connections could not be loaded')->body('Please try again or ask your administrator to check the platform connection.')->danger()->send();
        }
    }

    public function connect(): void
    {
        abort_unless(self::canAccess(), 403);
        $provider = app(PlatformConfiguration::class)->provider();
        Validator::make(['platform' => $this->platform], ['platform' => ['required', Rule::in($provider === 'zernio' ? ZernioTenantService::PLATFORMS : DirectPostPublisher::PLATFORMS)]])->validate();
        $team = Filament::getTenant();
        if ($provider === 'zernio') {
            try {
                $result = app(ZernioTenantService::class)->connectUrl($team, $this->platform, self::getUrl());
                $url = data_get($result, 'authUrl', data_get($result, 'url'));
                abort_unless(is_string($url) && str_starts_with($url, 'https://'), 422);
                $this->redirect($url);
            } catch (\Throwable $exception) {
                report($exception);
                Notification::make()->title('Connection could not be started')->danger()->send();
            }
        } else {
            $connection = OAuthConfiguration::query()->firstOrNew(['team_id' => $team->getKey(), 'user_id' => Filament::auth()->id(), 'service_name' => $this->platform]);
            $connection->forceFill(['account_name' => ucfirst($this->platform), 'is_active' => false])->save();
            $this->redirect(route('oauth.authenticate', ['service' => $this->platform, 'configId' => $connection->id]));
        }
    }

    public function disableConnection(int $id): void
    {
        abort_unless(self::canAccess(), 403);
        abort_unless(app(PlatformConfiguration::class)->provider() === 'direct', 422);
        OAuthConfiguration::query()->where('team_id', Filament::getTenant()->getKey())->findOrFail($id)->update(['is_active' => false]);
        $this->reloadAccounts();
        Notification::make()->title('Connection disabled for publishing')->success()->send();
    }

    public function saveDestination(int $id): void
    {
        abort_unless(self::canAccess(), 403);
        $data = Validator::make(['target' => $this->destinations[$id] ?? ''], ['target' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9:_-]+$/']])->validate();
        $connection = OAuthConfiguration::query()->where('team_id', Filament::getTenant()->getKey())->findOrFail($id);
        try {
            app(DirectPostPublisher::class)->configureDestination($connection, $data['target']);
        } catch (\RuntimeException $exception) {
            report($exception);
            Notification::make()->title('Destination could not be verified')->body('Reconnect the account and select a destination you manage.')->danger()->send();

            return;
        }
        Notification::make()->title('Publishing destination saved')->success()->send();
    }
}
