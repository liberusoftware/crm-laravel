<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Telephony\Events\CallLogged;
use Liberu\CRM\Telephony\Models\TelephonyCall;
use Liberu\CRM\Telephony\Models\TelephonyNumber;
use Liberu\CRM\Telephony\Services\TelephonyAudit;
use Liberu\CRM\Telephony\Services\TelephonyPolicy;

final class LogTelephonyCall
{
    public function execute(int $teamId, int $actorId, array $data): TelephonyCall
    {
        if (! app(TelephonyPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        $data = validator($data, ['from_number' => ['required', 'string', 'max:32'], 'to_number' => ['required', 'string', 'max:32'], 'direction' => ['nullable', 'in:inbound,outbound'], 'status' => ['nullable', 'string', 'max:50'], 'number_id' => ['nullable', 'integer', 'min:1'], 'contact_id' => ['nullable', 'integer', 'min:1'], 'started_at' => ['nullable', 'date'], 'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'], 'metadata' => ['nullable', 'array'], 'idempotency_key' => ['required', 'string', 'max:255']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data): TelephonyCall {
            if (isset($data['number_id']) && ! TelephonyNumber::query()->where('team_id', $teamId)->whereKey($data['number_id'])->exists()) {
                throw ValidationException::withMessages(['number_id' => 'The number does not belong to this team.']);
            }
            $existing = TelephonyCall::query()->where('team_id', $teamId)->where('idempotency_key', $data['idempotency_key'])->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }
            $call = TelephonyCall::query()->create(array_merge($data, ['team_id' => $teamId, 'direction' => $data['direction'] ?? 'inbound', 'status' => $data['status'] ?? 'completed']));
            app(TelephonyAudit::class)->record($teamId, $actorId, 'call_logged', ['call_id' => $call->id]);
            CallLogged::dispatch($call);

            return $call;
        });
    }
}
