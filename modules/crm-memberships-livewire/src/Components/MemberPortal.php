<?php

declare(strict_types=1);

namespace Liberu\CRM\MembershipsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Memberships\Queries\MembershipQuery;
use Livewire\Component;

final class MemberPortal extends Component
{
    public function render(): View
    {
        return app('view')->make('module-crm-memberships::portal', ['grants' => app(MembershipQuery::class)->memberGrants((int) auth()->user()->current_team_id, (int) auth()->id())->paginate(25)]);
    }
}
