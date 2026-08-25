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
}
