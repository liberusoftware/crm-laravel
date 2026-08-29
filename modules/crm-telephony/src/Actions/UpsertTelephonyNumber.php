<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Telephony\Models\TelephonyNumber;
use Liberu\CRM\Telephony\Services\TelephonyAudit;
use Liberu\CRM\Telephony\Services\TelephonyPolicy;

final class UpsertTelephonyNumber
{
    public function execute(int $teamId, int $actorId, array $data, ?int $id = null): TelephonyNumber
    {
        if (! app(TelephonyPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        } validator($data, ['number' => ['required', 'string', 'max:32'], 'label' => ['nullable', 'string', 'max:255'], 'provider' => ['required', 'string', 'max:50'], 'status' => ['nullable', 'in:active,inactive']])->validate();
        $number = TelephonyNumber::query()->updateOrCreate(['id' => $id, 'team_id' => $teamId], ['number' => $data['number'], 'label' => $data['label'] ?? null, 'provider' => $data['provider'], 'status' => $data['status'] ?? 'active', 'caller_id_enabled' => (bool) ($data['caller_id_enabled'] ?? true), 'metadata' => $data['metadata'] ?? null]);
        app(TelephonyAudit::class)->record($teamId, $actorId, 'number_updated', ['number_id' => $number->id]);

        return $number;
    }
}
