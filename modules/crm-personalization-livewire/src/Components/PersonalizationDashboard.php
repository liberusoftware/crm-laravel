<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Personalization\Queries\PersonalizationQuery;
use Livewire\Component;

final class PersonalizationDashboard extends Component
{
    public function render(PersonalizationQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-personalization-livewire::dashboard', ['rules' => $query->rules((int) $id)->get(), 'decisions' => $query->decisions((int) $id)->limit(25)->get(), 'outcomes' => $query->outcomes((int) $id)->limit(25)->get()]);
    }
}
