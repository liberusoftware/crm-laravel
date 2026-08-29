<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingLivewire\Livewire;

use Liberu\CRM\EmailMarketing\Queries\EmailMarketingQuery;
use Livewire\Component;

final class CampaignDashboard extends Component
{
    public ?int $campaignId = null;

    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;
        $query = app(EmailMarketingQuery::class);

        return app('view')->make('module-crm-email-marketing-livewire::dashboard', ['campaigns' => $query->campaigns($teamId)->limit(25)->get(), 'analytics' => $this->campaignId === null ? [] : $query->analytics($teamId, $this->campaignId)]);
    }
}
