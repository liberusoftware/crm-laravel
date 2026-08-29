<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Telephony\Models\TelephonyCall;
use Liberu\CRM\Telephony\Services\TelephonyAudit;
use Liberu\CRM\Telephony\Services\TelephonyPolicy;

final class UpdateCall
{
    public function execute(int $teamId, int $actorId, int $callId, array $data): TelephonyCall
    {
        if (! app(TelephonyPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        $call = TelephonyCall::query()->where('team_id', $teamId)->findOrFail($callId);
        $data = validator($data, ['disposition' => ['nullable', 'string', 'max:100'], 'recording_url' => ['nullable', 'url', 'max:2048'], 'voicemail_url' => ['nullable', 'url', 'max:2048'], 'transfer_to' => ['nullable', 'string', 'max:32']])->validate();
        $call->fill($data)->save();
        app(TelephonyAudit::class)->record($teamId, $actorId, 'call_updated', ['call_id' => $call->id, 'fields' => array_keys($data)]);

        return $call;
    }
}
