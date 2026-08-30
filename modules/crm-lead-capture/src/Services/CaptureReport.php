<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Services;

use Illuminate\Support\Carbon;
use Liberu\CRM\LeadCapture\Models\LeadCapture;

final class CaptureReport
{
    /** @return array<string, int|float> */
    public function summarize(int $teamId, Carbon $from, Carbon $until): array
    {
        $query = LeadCapture::query()->where('team_id', $teamId)->whereBetween('captured_at', [$from, $until]);
        $total = (int) (clone $query)->count();
        $converted = (int) (clone $query)->where('status', 'converted')->count();

        return ['total' => $total, 'converted' => $converted, 'failed' => (int) (clone $query)->where('status', 'failed')->count(), 'conversion_rate' => $total === 0 ? 0.0 : round($converted / $total * 100, 2)];
    }
}
