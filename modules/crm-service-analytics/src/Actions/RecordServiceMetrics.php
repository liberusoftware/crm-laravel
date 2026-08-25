<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\ServiceAnalytics\Services\AnalyticsAudit;
use Liberu\CRM\ServiceAnalytics\Services\AnalyticsPolicy;

final class RecordServiceMetrics
{
    public function execute(int $teamId, int $actorId, array $metrics): int
    {
        if (! app(AnalyticsPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }$count = 0;
        DB::transaction(function () use ($teamId, $actorId, $metrics, &$count) {
            $action = app(RecordMetric::class);
            foreach ($metrics as $metric) {
                $action->execute($teamId, $actorId, $metric);
                $count++;
            }app(AnalyticsAudit::class)->record($teamId, $actorId, 'metrics_batch_recorded', ['count' => $count]);
        });

        return $count;
    }
}
