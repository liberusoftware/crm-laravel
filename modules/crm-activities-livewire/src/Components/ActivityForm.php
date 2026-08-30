<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Activities\Actions\CompleteActivities;
use Liberu\CRM\Activities\Actions\CreateActivity;
use Livewire\Component;

final class ActivityForm extends Component
{
    public string $kind = 'task';

    public string $title = '';

    public string $description = '';

    public ?string $dueAt = null;

    public ?int $activityId = null;

    public function save(CreateActivity $create): void
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $this->validate(['kind' => 'required|in:task,call,meeting,email', 'title' => 'required|string|max:180', 'description' => 'nullable|string', 'dueAt' => 'nullable|date']);
        $create->execute($teamId, auth()->id(), ['kind' => $this->kind, 'title' => $this->title, 'description' => $this->description, 'due_at' => $this->dueAt]);
        $this->reset(['title', 'description', 'dueAt']);
        $this->dispatch('activity-created');
    }

    public function complete(CompleteActivities $complete): void
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && $this->activityId !== null, 403);
        $complete->execute($teamId, [$this->activityId]);
        $this->dispatch('activity-completed');
    }

    public function render(): View
    {
        return app('view')->make('crm-activities-livewire::activity-form');
    }
}
