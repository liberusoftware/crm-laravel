<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Segmentation\Models\BehaviorEvent;
use Liberu\CRM\Segmentation\Services\SegmentationPolicy;

final class RecordBehaviorEvent
{
    public function execute(int $teamId, int $actorId, array $data): BehaviorEvent
    {
        if (! app(SegmentationPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['contact_id' => ['required', 'integer'], 'event' => ['required', 'string', 'max:100'], 'properties' => ['nullable', 'array'], 'occurred_at' => ['nullable', 'date']])->validate();

        return BehaviorEvent::query()->create(['team_id' => $teamId, 'contact_id' => $data['contact_id'], 'event' => $data['event'], 'properties' => $data['properties'] ?? [], 'occurred_at' => $data['occurred_at'] ?? now()]);
    }
}
