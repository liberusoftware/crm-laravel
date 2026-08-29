<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Services;

use Liberu\CRM\Segmentation\Models\SegmentationLineage;

final class SegmentationAudit
{
    public function record(int $teamId, ?int $actorId, int $audienceId, string $operation, array $details = []): SegmentationLineage
    {
        return SegmentationLineage::query()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'audience_id' => $audienceId, 'operation' => $operation, 'source_type' => 'segmentation', 'details' => $details]);
    }
}
