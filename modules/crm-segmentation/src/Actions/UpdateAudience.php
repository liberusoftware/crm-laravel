<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Segmentation\Models\Audience;
use Liberu\CRM\Segmentation\Services\SegmentationAudit;
use Liberu\CRM\Segmentation\Services\SegmentationPolicy;

final class UpdateAudience
{
    public function execute(int $teamId, int $actorId, int $audienceId, array $data): Audience
    {
        if (! app(SegmentationPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }$audience = Audience::query()->where('team_id', $teamId)->findOrFail($audienceId);
        validator($data, ['conditions' => ['nullable', 'array'], 'exclusions' => ['nullable', 'array'], 'calculated_attributes' => ['nullable', 'array'], 'status' => ['nullable', 'in:draft,active,paused']])->validate();
        $audience->fill($data)->save();
        app(SegmentationAudit::class)->record($teamId, $actorId, $audience->id, 'audience_updated', ['fields' => array_keys($data)]);

        return $audience;
    }
}
