<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesPipelines\Events\OpportunityChanged;
use Liberu\CRM\SalesPipelines\Models\Opportunity;
use Liberu\CRM\SalesPipelines\Models\SalesStage;
use Liberu\CRM\SalesPipelines\Models\StageHistory;
use Liberu\CRM\SalesPipelines\Services\PipelineAudit;
use Liberu\CRM\SalesPipelines\Services\PipelinePolicy;

final class MoveOpportunity
{
    public function execute(int $teamId, int $actorId, int $id, array $data): Opportunity
    {
        if (! app(PipelinePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['stage_id' => ['required', 'integer']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $id, $data) {
            $opp = Opportunity::query()->where('team_id', $teamId)->lockForUpdate()->findOrFail($id);
            $stage = SalesStage::query()->whereKey($data['stage_id'])->where('pipeline_id', $opp->pipeline_id)->firstOrFail();
            $old = $opp->stage_id;
            $opp->stage_id = $stage->id;
            $opp->probability = $stage->probability;
            $opp->last_stage_at = now();
            $opp->save();
            StageHistory::query()->create(['team_id' => $teamId, 'opportunity_id' => $opp->id, 'from_stage_id' => $old, 'to_stage_id' => $stage->id, 'actor_id' => $actorId, 'entered_at' => now()]);
            app(PipelineAudit::class)->record($teamId, $actorId, 'opportunity_moved', ['opportunity_id' => $opp->id]);
            OpportunityChanged::dispatch($opp, 'moved');

            return $opp;
        });
    }
}
