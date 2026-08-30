<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\LeadCapture\Models\CaptureReferral;

final class RecordReferral
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, ?int $actorId, array $attributes): CaptureReferral
    {
        return DB::transaction(fn (): CaptureReferral => CaptureReferral::query()->create(array_merge($attributes, ['team_id' => $teamId, 'actor_id' => $actorId]))->refresh());
    }
}
