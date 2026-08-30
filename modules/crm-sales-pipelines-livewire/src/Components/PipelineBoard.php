<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\SalesPipelines\Actions\MoveOpportunity;
use Liberu\CRM\SalesPipelines\Models\SalesStage;
use Liberu\CRM\SalesPipelines\Queries\PipelineQuery;
use Livewire\Component;

final class PipelineBoard extends Component
{
    public function render(PipelineQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        $opportunities = $query->opportunities((int) $id)->limit(100)->get();
        $stageIds = $opportunities->pluck('stage_id')->filter()->unique();
        $stages = SalesStage::query()->whereIn('id', $stageIds)->orderBy('position')->get()->keyBy('id');

        return app('view')->make('crm-sales-pipelines-livewire::board', compact('opportunities', 'stages'));
    }

    public function move(int $opportunityId, int $stageId, MoveOpportunity $action): void
    {
        $user = auth()->user();
        $teamId = $user?->current_team_id;
        abort_unless($teamId !== null && $user !== null, 403);

        $action->execute((int) $teamId, (int) $user->id, $opportunityId, ['stage_id' => $stageId]);
    }
}
