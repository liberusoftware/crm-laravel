<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Telephony\Models\TelephonySettings;
use Liberu\CRM\Telephony\Services\TelephonyAudit;
use Liberu\CRM\Telephony\Services\TelephonyPolicy;

final class ConfigureTelephony
{
    public function execute(int $teamId, int $actorId, array $data): TelephonySettings
    {
        if (! app(TelephonyPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        $data = validator($data, ['provider' => ['required', 'in:twilio'], 'account_sid' => ['nullable', 'string', 'regex:/^AC[a-zA-Z0-9]{32}$/'], 'auth_token' => ['nullable', 'string', 'max:255'], 'messaging_service_sid' => ['nullable', 'string', 'regex:/^MG[a-zA-Z0-9]{32}$/'], 'default_from_number' => ['nullable', 'string', 'max:32'], 'business_hours' => ['nullable', 'array'], 'ivr' => ['nullable', 'array'], 'skills' => ['nullable', 'array']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data): TelephonySettings {
            $settings = TelephonySettings::query()->where('team_id', $teamId)->lockForUpdate()->first();
            $settings ??= new TelephonySettings(['team_id' => $teamId, 'version' => 0]);
            $settings->fill(['provider' => $data['provider'], 'account_sid' => $data['account_sid'] ?? $settings->account_sid, 'auth_token' => $data['auth_token'] ?? $settings->auth_token, 'messaging_service_sid' => $data['messaging_service_sid'] ?? $settings->messaging_service_sid, 'default_from_number' => $data['default_from_number'] ?? $settings->default_from_number, 'business_hours' => $data['business_hours'] ?? [], 'ivr' => $data['ivr'] ?? [], 'skills' => $data['skills'] ?? [], 'version' => ((int) $settings->version) + 1]);
            $settings->save();
            app(TelephonyAudit::class)->record($teamId, $actorId, 'telephony_configured', ['version' => $settings->version]);

            return $settings;
        });
    }
}
