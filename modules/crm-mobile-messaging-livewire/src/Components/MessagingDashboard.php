<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessagingLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\MobileMessaging\Queries\CampaignQuery;
use Livewire\Component;

final class MessagingDashboard extends Component
{
    public function render(): View
    {
        return app('view')->make('module-crm-mobile-messaging::dashboard', ['campaigns' => app(CampaignQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
