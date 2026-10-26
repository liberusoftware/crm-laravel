<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceLivewire\Livewire;

use Liberu\CRM\ProductWorkspace\Queries\ProductWorkspaceQuery;
use Livewire\Component;

final class ProductDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-product-workspace-livewire::dashboard', ['products' => app(ProductWorkspaceQuery::class)->products($teamId)->limit(25)->get()]);
    }
}
