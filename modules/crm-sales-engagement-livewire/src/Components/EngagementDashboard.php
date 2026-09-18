<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Livewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Actions\CompleteEngagementTask;
use Liberu\CRM\SalesEngagement\Queries\EngagementQuery;
use Livewire\Component;

final class EngagementDashboard extends Component
{
    public function completeTask(int $taskId, CompleteEngagementTask $action): void
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless($teamId !== null, 403);
        $this->resetErrorBag();

        try {
            $action->execute((int) $teamId, (int) auth()->id(), $taskId);
        } catch (ValidationException $exception) {
            $this->addError('task', $exception->getMessage());
        }
    }

    public function render(EngagementQuery $query): View
    {
        $id = auth()->user()?->getAttribute('current_team_id');
        abort_unless($id !== null, 403);

        return app('view')->make('crm-sales-engagement-livewire::dashboard', ['sequences' => $query->sequences((int) $id)->get(), 'enrollments' => $query->enrollments((int) $id)->limit(25)->get(), 'tasks' => $query->tasks((int) $id)->limit(25)->get()]);
    }
}
