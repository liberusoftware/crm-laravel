<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Models\MarketingPlatformSettings;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

final class PlatformConfiguration
{
    public function settings(): ?MarketingPlatformSettings
    {
        return Schema::hasTable('marketing_platform_settings') ? MarketingPlatformSettings::query()->find(1) : null;
    }

    public function provider(): string
    {
        return $this->settings()?->getAttribute('provider')
            ?? (in_array(config('services.zernio.mode'), ['zernio', 'preferred'], true) ? 'zernio' : 'direct');
    }

    public function credential(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings()?->getAttribute('credentials') ?? [], $key, $default);
    }

    public function save(User $actor, array $input): void
    {
        abort_unless($actor->isSuperAdmin(), 403);
        $rules = [
            'provider' => ['required', 'in:direct,zernio'],
            'zernio_api_key' => ['nullable', 'string', 'max:1000'],
            'zernio_webhook_secret' => ['nullable', 'string', 'max:1000'],
            'ai_endpoint' => ['nullable', 'url:https', 'max:2048'],
            'ai_api_key' => ['nullable', 'string', 'max:1000'],
            'ai_enabled' => ['required', 'boolean'],
            'meta_api_version' => ['nullable', 'regex:/^v[0-9]+\.[0-9]+$/'],
            'linkedin_api_version' => ['nullable', 'regex:/^[0-9]{6}$/'],
        ];
        foreach (['facebook', 'google', 'linkedin', 'twitter', 'microsoft', 'twilio', 'whatsapp', 'mailchimp'] as $provider) {
            $rules[$provider.'_client_id'] = ['nullable', 'string', 'max:1000'];
            $rules[$provider.'_client_secret'] = ['nullable', 'string', 'max:4000'];
        }
        $data = Validator::make($input, $rules)->validate();
        $settings = $this->settings() ?? new MarketingPlatformSettings(['id' => 1]);
        $credentials = $settings->getAttribute('credentials') ?? [];
        foreach ($data as $key => $value) {
            if (! in_array($key, ['provider', 'ai_enabled'], true) && filled($value)) {
                $credentials[$key] = $value;
            }
        }
        Validator::make($credentials, [
            'zernio_api_key' => $data['provider'] === 'zernio' ? ['required', 'string'] : ['nullable', 'string'],
            'ai_endpoint' => $data['ai_enabled'] ? ['required', 'url:https'] : ['nullable', 'url:https'],
            'ai_api_key' => $data['ai_enabled'] ? ['required', 'string'] : ['nullable', 'string'],
        ])->validate();
        $settings->fill(['provider' => $data['provider'], 'credentials' => $credentials, 'options' => ['ai_enabled' => (bool) $data['ai_enabled']], 'setup_completed_at' => now()])->save();
    }
}
