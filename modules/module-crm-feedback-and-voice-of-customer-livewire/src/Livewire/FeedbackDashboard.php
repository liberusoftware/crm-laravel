<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomerLivewire\Livewire;

use Liberu\CRM\FeedbackAndVoiceOfCustomer\Queries\FeedbackQuery;
use Livewire\Component;

final class FeedbackDashboard extends Component
{
    public ?int $surveyId = null;

    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;
        $query = app(FeedbackQuery::class);

        return app('view')->make('module-crm-feedback-and-voice-of-customer-livewire::dashboard', ['surveys' => $query->surveys($teamId)->get(), 'trend' => $this->surveyId === null ? [] : $query->trend($teamId, $this->surveyId)]);
    }
}
