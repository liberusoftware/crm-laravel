<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\ServiceAnalytics\Events\MetricRecorded;
use Liberu\CRM\ServiceAnalytics\Models\AnalyticsSnapshot;
use Liberu\CRM\ServiceAnalytics\Services\AnalyticsAudit;
use Liberu\CRM\ServiceAnalytics\Services\AnalyticsPolicy;

final class RecordMetric
{
    private const METRICS = ['volume', 'backlog', 'deflection', 'first_response', 'resolution', 'reopen', 'transfer', 'sla', 'satisfaction', 'quality', 'staffing', 'cost_to_serve'];

    public function execute(int $teamId, int $actorId, array $data): AnalyticsSnapshot
    {
        if (! app(AnalyticsPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['metric' => ['required', 'in:'.implode(',', self::METRICS)], 'period_start' => ['required', 'date'], 'period_end' => ['required', 'date', 'after:period_start'], 'value' => ['required', 'numeric'], 'dimensions' => ['nullable', 'array'], 'source' => ['nullable', 'string', 'max:100']])->validate();
        $dimensions = $data['dimensions'] ?? [];
        ksort($dimensions);
        $hash = hash('sha256', (string) json_encode($dimensions, JSON_THROW_ON_ERROR));

        return DB::transaction(function () use ($teamId, $actorId, $data, $dimensions, $hash) {
            $snapshot = AnalyticsSnapshot::query()->updateOrCreate(['team_id' => $teamId, 'metric' => $data['metric'], 'period_start' => $data['period_start'], 'period_end' => $data['period_end'], 'dimensions_hash' => $hash], ['value' => $data['value'], 'dimensions' => $dimensions, 'source' => $data['source'] ?? null, 'recorded_by' => $actorId, 'generated_at' => now()]);
            app(AnalyticsAudit::class)->record($teamId, $actorId, 'metric_recorded', ['snapshot_id' => $snapshot->id, 'metric' => $snapshot->metric]);
            MetricRecorded::dispatch($snapshot);

            return $snapshot;
        });
    }
}
