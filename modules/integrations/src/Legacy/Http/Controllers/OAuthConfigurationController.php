<?php

namespace App\Http\Controllers;

use App\Models\OAuthConfiguration;
use App\Services\Marketing\PlatformConfiguration;
use App\Services\Marketing\WorkspaceAccess;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class OAuthConfigurationController extends Controller
{
    /**
     * Maps the user-facing service name to the Socialite driver name
     * and the scopes needed for posting content.
     */
    protected $serviceToDriver = [
        'facebook' => 'facebook',
        'twitter' => 'twitter-oauth-2',
        'instagram' => 'facebook',        // Instagram posting uses Facebook Graph API
        'linkedin' => 'linkedin-openid',
        'youtube' => 'google',          // YouTube uses Google OAuth
        'google' => 'google',
        'gmail' => 'google',
        'whatsapp' => 'whatsapp',
        'outlook' => 'microsoft',
        'microsoft365' => 'microsoft',
    ];

    protected $serviceScopes = [
        'facebook' => [
            'email',
            'pages_show_list',
            'pages_read_engagement',
            'pages_manage_posts',
            'publish_to_groups',
            'instagram_basic',
            'instagram_content_publish',
        ],
        'twitter' => [
            'tweet.read',
            'tweet.write',
            'users.read',
            'offline.access',
        ],
        'instagram' => [
            'email',
            'pages_show_list',
            'pages_read_engagement',
            'instagram_basic',
            'instagram_content_publish',
        ],
        'linkedin' => [
            'openid',
            'profile',
            'email',
            'w_member_social',
        ],
        'youtube' => [
            'https://www.googleapis.com/auth/youtube.upload',
            'https://www.googleapis.com/auth/youtube',
        ],
        'google' => [
            'https://www.googleapis.com/auth/gmail.modify',
            'https://www.googleapis.com/auth/calendar',
        ],
        'gmail' => [
            'https://www.googleapis.com/auth/gmail.modify',
            'https://www.googleapis.com/auth/calendar',
        ],
    ];

    public function index(): Factory|View
    {
        $configurations = OAuthConfiguration::where('user_id', Auth::id())->get();

        return view('oauth.configurations.index', ['configurations' => $configurations]);
    }

    public function create(): Factory|View
    {
        return view('oauth.configurations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|in:facebook,twitter,instagram,linkedin,youtube,google,gmail,whatsapp,outlook,microsoft365',
            'account_name' => 'required|string',
            'additional_settings' => 'nullable|array',
        ]);

        $team = Auth::user()->currentTeam;
        abort_unless($team && app(WorkspaceAccess::class)->canManage(Auth::user(), $team), 403);
        $config = new OAuthConfiguration($validated);
        $config->setAttribute('team_id', $team->getKey());
        $config->user_id = Auth::id();
        $config->save();

        return redirect()->route('oauth.authenticate', [
            'service' => $config->service_name,
            'configId' => $config->id,
        ]);
    }

    public function authenticate($service, $configId)
    {
        $config = OAuthConfiguration::query()->whereKey($configId)->where('team_id', Auth::user()->getAttribute('current_team_id'))->where('user_id', Auth::id())->where('service_name', $service)->firstOrFail();
        if (in_array($service, ['facebook', 'instagram', 'linkedin', 'twitter', 'youtube'], true)) {
            abort_unless(app(PlatformConfiguration::class)->provider() === 'direct', 422);
        }
        session(['oauth_config_id' => $configId, 'oauth_config_service' => $service]);

        $driver = $this->serviceToDriver[$service] ?? $service;

        try {
            $platform = app(PlatformConfiguration::class);
            $credentialProvider = match ($service) {
                'instagram' => 'facebook', 'youtube', 'google', 'gmail' => 'google', 'outlook', 'microsoft365' => 'microsoft', default => $service
            };
            $clientId = $platform->credential($credentialProvider.'_client_id');
            $clientSecret = $platform->credential($credentialProvider.'_client_secret');
            if ($clientId && $clientSecret) {
                config(['services.'.$driver.'.client_id' => $clientId, 'services.'.$driver.'.client_secret' => $clientSecret]);
            }
            config(['services.'.$driver.'.redirect' => route('oauth.configurations.callback', ['service' => $service])]);
            Socialite::forgetDrivers();
            $socialite = Socialite::driver($driver);

            if (isset($this->serviceScopes[$service]) && $socialite instanceof AbstractProvider) {
                $socialite->scopes($this->serviceScopes[$service]);
            }

            if ($driver === 'google' && $socialite instanceof AbstractProvider) {
                $socialite->with(['access_type' => 'offline', 'prompt' => 'consent']);
            }

            return $socialite->redirect();
        } catch (\Exception $e) {
            Log::error("OAuth redirect failed for {$service}: ".$e->getMessage());

            return redirect()->route('oauth.configurations.index')
                ->with('error', 'Failed to start OAuth for '.ucfirst((string) $service).'. Please ensure the credentials are configured in settings.');
        }
    }

    public function callback($service)
    {
        try {
            abort_unless(session('oauth_config_service') === $service, 403);
            $configId = session()->pull('oauth_config_id');
            session()->forget('oauth_config_service');
            $config = OAuthConfiguration::query()->whereKey($configId)->where('user_id', Auth::id())->where('team_id', Auth::user()->getAttribute('current_team_id'))->where('service_name', $service)->firstOrFail();

            $driver = $this->serviceToDriver[$service] ?? $service;
            $platform = app(PlatformConfiguration::class);
            $credentialProvider = match ($service) {
                'instagram' => 'facebook', 'youtube', 'google', 'gmail' => 'google', 'outlook', 'microsoft365' => 'microsoft', default => $service
            };
            if ($platform->credential($credentialProvider.'_client_id') && $platform->credential($credentialProvider.'_client_secret')) {
                config(['services.'.$driver.'.client_id' => $platform->credential($credentialProvider.'_client_id'), 'services.'.$driver.'.client_secret' => $platform->credential($credentialProvider.'_client_secret')]);
            }
            config(['services.'.$driver.'.redirect' => route('oauth.configurations.callback', ['service' => $service])]);
            Socialite::forgetDrivers();
            $socialiteUser = Socialite::driver($driver)->user();
            if (! $socialiteUser instanceof User) {
                throw new \RuntimeException('This provider did not return a bearer-token account.');
            }

            $additionalSettings = array_merge($config->additional_settings ?? [], [
                'provider_id' => $socialiteUser->getId(),
                'email' => $socialiteUser->getEmail(),
                'name' => $socialiteUser->getName(),
                'avatar' => $socialiteUser->getAvatar(),
            ]);

            $updateData = [
                'is_active' => true,
                'additional_settings' => $additionalSettings,
            ];

            // Store tokens if the columns exist (added by migration)
            if (Schema::hasColumn('oauth_configurations', 'access_token')) {
                $updateData['access_token'] = $socialiteUser->token;
            }
            if (Schema::hasColumn('oauth_configurations', 'refresh_token')) {
                $updateData['refresh_token'] = $socialiteUser->refreshToken;
            }
            if (Schema::hasColumn('oauth_configurations', 'token_expires_at')) {
                $updateData['token_expires_at'] = property_exists($socialiteUser, 'expiresIn') && $socialiteUser->expiresIn !== null
                    ? now()->addSeconds((int) $socialiteUser->expiresIn)
                    : null;
            }

            $config->update($updateData);

            return redirect()->route('oauth.configurations.index')
                ->with('success', ucfirst((string) $service).' account connected successfully!');
        } catch (\Exception $e) {
            Log::error("OAuth callback failed for {$service}: ".$e->getMessage());

            return redirect()->route('oauth.configurations.index')
                ->with('error', 'The account could not be connected. Please try again or contact your administrator.');
        }
    }

    public function destroy(OAuthConfiguration $configuration)
    {
        abort_unless($configuration->user_id === Auth::id(), 403);
        $configuration->delete();

        return redirect()->route('oauth.configurations.index')
            ->with('success', 'Configuration removed successfully');
    }
}
