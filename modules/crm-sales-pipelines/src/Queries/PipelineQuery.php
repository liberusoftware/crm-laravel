<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Queries;

use Liberu\CRM\SalesPipelines\Models\Opportunity;
use Liberu\CRM\SalesPipelines\Models\SalesPipeline;
use Liberu\CRM\SalesPipelines\Models\SalesStage;

final class PipelineQuery
{
    public function pipelines(int $teamId)
    {
        return SalesPipeline::query()->where('team_id', $teamId)->latest();
    }

    public function stages(int $teamId, int $pipelineId)
    {
        $valid = SalesPipeline::query()->where('team_id', $teamId)->whereKey($pipelineId)->exists();

        return SalesStage::query()->when(! $valid, fn ($q) => $q->whereKey(0))->where('pipeline_id', $pipelineId)->orderBy('position');
    }

    public function opportunities(int $teamId)
    {
        return Opportunity::query()->where('team_id', $teamId)->latest();
    }

    public function rottingOpportunities(int $teamId)
    {
        $stages = SalesStage::query()->whereHas('pipeline', fn ($query) => $query->where('team_id', $teamId))
            ->whereNotNull('rotting_days')->get(['id', 'rotting_days']);

        if ($stages->isEmpty()) {
            return Opportunity::query()->whereKey(0);
        }

        return Opportunity::query()->where('team_id', $teamId)->where('status', 'open')
            ->where(function ($query) use ($stages): void {
                foreach ($stages as $stage) {
                    $query->orWhere(function ($stageQuery) use ($stage): void {
                        $stageQuery->where('stage_id', $stage->id)
                            ->whereNotNull('last_stage_at')
                            ->where('last_stage_at', '<=', now()->subDays((int) $stage->rotting_days));
                    });
                }
            })->latest('last_stage_at');
    }
}
