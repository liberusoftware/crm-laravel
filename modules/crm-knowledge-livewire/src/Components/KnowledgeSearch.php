<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Knowledge\Queries\KnowledgeQuery;
use Livewire\Component;

final class KnowledgeSearch extends Component
{
    public string $search = '';

    public function render(): View
    {
        return app('view')->make('module-crm-knowledge::search', ['articles' => app(KnowledgeQuery::class)->forTeam((int) auth()->user()->current_team_id, $this->search)->paginate(25)]);
    }
}
