<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\SalesPipelines\Queries\PipelineQuery;
use Livewire\Component;

final class PipelineBoard extends Component
{
    public function render(PipelineQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-sales-pipelines-livewire::board', ['opportunities' => $query->opportunities((int) $id)->limit(50)->get()]);
    }
}
