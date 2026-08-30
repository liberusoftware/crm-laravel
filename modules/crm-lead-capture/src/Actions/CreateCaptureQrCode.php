<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\LeadCapture\Models\CaptureQrCode;

final class CreateCaptureQrCode
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, ?int $actorId, array $attributes): CaptureQrCode
    {
        return DB::transaction(fn (): CaptureQrCode => CaptureQrCode::query()->create(array_merge($attributes, ['team_id' => $teamId, 'actor_id' => $actorId, 'status' => $attributes['status'] ?? 'active']))->refresh());
    }
}
