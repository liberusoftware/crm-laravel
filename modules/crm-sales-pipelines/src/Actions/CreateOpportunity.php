<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesPipelines\Events\OpportunityChanged;
use Liberu\CRM\SalesPipelines\Models\Opportunity;
use Liberu\CRM\SalesPipelines\Models\SalesPipeline;
use Liberu\CRM\SalesPipelines\Models\SalesStage;
use Liberu\CRM\SalesPipelines\Services\PipelineAudit;
use Liberu\CRM\SalesPipelines\Services\PipelinePolicy;

final class CreateOpportunity
{
    public function execute(int $teamId, int $actorId, array $data): Opportunity
    {
        if (! app(PipelinePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['pipeline_id' => ['required', 'integer'], 'stage_id' => ['required', 'integer'], 'name' => ['required', 'string', 'max:255'], 'value' => ['nullable', 'numeric', 'min:0'], 'probability' => ['nullable', 'numeric', 'between:0,100'], 'close_date' => ['nullable', 'date'], 'products' => ['nullable', 'array'], 'competitors' => ['nullable', 'array'], 'dependencies' => ['nullable', 'array']])->validate();
        if (! SalesPipeline::query()->where('team_id', $teamId)->whereKey($data['pipeline_id'])->exists() || ! SalesStage::query()->whereKey($data['stage_id'])->where('pipeline_id', $data['pipeline_id'])->exists()) {
            throw ValidationException::withMessages(['pipeline_id' => 'Pipeline or stage is invalid for this team.']);
        }$opp = Opportunity::query()->create(array_merge($data, ['team_id' => $teamId, 'status' => 'open', 'value' => $data['value'] ?? 0, 'probability' => $data['probability'] ?? 0, 'last_stage_at' => now()]));
        app(PipelineAudit::class)->record($teamId, $actorId, 'opportunity_created', ['opportunity_id' => $opp->id]);
        OpportunityChanged::dispatch($opp, 'created');

        return $opp;
    }
}
