<?php

declare(strict_types=1);

namespace Liberu\CRM\DocumentsLivewire\Livewire;

use Liberu\CRM\Documents\Queries\DocumentsQuery;
use Livewire\Component;

final class DocumentsDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-documents-livewire::dashboard', ['documents' => app(DocumentsQuery::class)->documents($teamId)->limit(25)->get()]);
    }
}
