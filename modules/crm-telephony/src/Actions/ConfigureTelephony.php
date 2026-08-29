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

        $data = validator($data, ['provider' => ['required', 'string', 'max:50'], 'business_hours' => ['nullable', 'array'], 'ivr' => ['nullable', 'array'], 'skills' => ['nullable', 'array']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data): TelephonySettings {
            $settings = TelephonySettings::query()->where('team_id', $teamId)->lockForUpdate()->first();
            $settings ??= new TelephonySettings(['team_id' => $teamId, 'version' => 0]);
            $settings->fill(['provider' => $data['provider'], 'business_hours' => $data['business_hours'] ?? [], 'ivr' => $data['ivr'] ?? [], 'skills' => $data['skills'] ?? [], 'version' => ((int) $settings->version) + 1]);
            $settings->save();
            app(TelephonyAudit::class)->record($teamId, $actorId, 'telephony_configured', ['version' => $settings->version]);

            return $settings;
        });
    }
}
