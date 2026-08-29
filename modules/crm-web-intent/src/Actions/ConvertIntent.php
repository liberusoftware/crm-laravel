<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WebIntent\Models\WebIntentConversion;
use Liberu\CRM\WebIntent\Models\WebIntentVisit;
use Liberu\CRM\WebIntent\Services\WebIntentAudit;
use Liberu\CRM\WebIntent\Services\WebIntentPolicy;

final class ConvertIntent
{
    /** @param array<string, mixed> $metadata */
    public function execute(int $teamId, int $actorId, string $visitorKey, string $targetType, int $targetId, ?int $visitId, array $metadata, WebIntentPolicy $policy): WebIntentConversion
    {
        abort_unless($policy->canManage($teamId, $actorId), 403, 'You cannot convert web intent for this team.');
        validator(compact('visitorKey', 'targetType', 'targetId'), ['visitorKey' => ['required', 'string', 'max:128'], 'targetType' => ['required', 'string', 'max:100'], 'targetId' => ['required', 'integer', 'min:1']])->validate();
        if ($visitId !== null && ! WebIntentVisit::query()->where('team_id', $teamId)->whereKey($visitId)->exists()) {
            throw ValidationException::withMessages(['visitId' => 'The visit does not belong to this team.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $visitorKey, $targetType, $targetId, $visitId, $metadata): WebIntentConversion {
            $conversion = WebIntentConversion::query()->firstOrCreate(['team_id' => $teamId, 'visitor_key' => $visitorKey, 'target_type' => $targetType, 'target_id' => $targetId], ['visit_id' => $visitId, 'actor_id' => $actorId, 'metadata' => $metadata, 'status' => 'completed']);
            app(WebIntentAudit::class)->record($teamId, $actorId, 'intent_converted', ['visitor_key' => $visitorKey, 'target_type' => $targetType, 'target_id' => $targetId]);

            return $conversion;
        });
    }
}
