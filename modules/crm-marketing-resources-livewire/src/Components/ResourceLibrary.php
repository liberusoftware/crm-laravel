<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResourcesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\MarketingResources\Queries\MarketingResourceQuery;
use Livewire\Component;

final class ResourceLibrary extends Component
{
    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $teamId > 0, 403);

        return app('view')->make('module-crm-marketing-resources::library', ['resources' => app(MarketingResourceQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
