<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Services;

use Illuminate\Support\Facades\DB;

final class WebIntentAudit
{
    /** @param array<string, mixed> $data */
    public function record(int $teamId, ?int $actorId, string $event, array $data = []): void
    {
        $details = $this->redact($data);
        DB::table('crm_web_intent_audits')->insert(['team_id' => $teamId, 'actor_id' => $actorId, 'event' => $event, 'details' => json_encode($details, JSON_THROW_ON_ERROR), 'request_id' => request()->header('X-Request-ID'), 'created_at' => now(), 'updated_at' => now()]);
        logger()->info('crm.web_intent.'.$event, ['team_id' => $teamId, 'actor_id' => $actorId, 'data' => $details]);
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    private function redact(array $data): array
    {
        foreach (['email', 'ip', 'token', 'secret'] as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = '[REDACTED]';
            }
        }

        return $data;
    }
}
