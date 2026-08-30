<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\PredictiveModels\Queries\PredictiveModelQuery;
use Livewire\Component;

final class PredictiveModelsDashboard extends Component
{
    public function render(PredictiveModelQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-predictive-models-livewire::dashboard', ['models' => $query->models((int) $id)->get(), 'predictions' => $query->predictions((int) $id)->limit(25)->get(), 'drift' => $query->drift((int) $id)->limit(25)->get()]);
    }
}
