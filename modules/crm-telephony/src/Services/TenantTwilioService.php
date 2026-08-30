<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Services;

use Liberu\CRM\Telephony\Models\TelephonySettings;
use Twilio\Rest\Client;

final class TenantTwilioService
{
    public function client(int $teamId): Client
    {
        $settings = TelephonySettings::query()->where('team_id', $teamId)->first();
        if (! $settings?->account_sid || ! $settings?->auth_token) {
            throw new \RuntimeException('Twilio credentials have not been configured for this team.');
        }

        return new Client($settings->account_sid, $settings->auth_token);
    }

    public function sendMessage(int $teamId, string $to, string $body): object
    {
        $settings = TelephonySettings::query()->where('team_id', $teamId)->firstOrFail();
        $from = $settings->messaging_service_sid ? ['messagingServiceSid' => $settings->messaging_service_sid] : ['from' => $settings->default_from_number];
        if (($from['from'] ?? null) === null) {
            throw new \RuntimeException('A Twilio messaging service or sender number is required.');
        }

        return $this->client($teamId)->messages->create($to, array_merge($from, ['body' => $body]));
    }

    public function usage(int $teamId, array $filters = []): array
    {
        return $this->client($teamId)->usage->records->read($filters);
    }
}
