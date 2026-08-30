<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\LeadCapture\Actions\CaptureLead;
use Livewire\Component;

final class CaptureForm extends Component
{
    public string $kind = 'manual';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $source = 'manual';

    public function save(CaptureLead $action): void
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $this->validate(['kind' => 'required|string', 'name' => 'nullable|string|max:255', 'email' => 'nullable|email|max:255', 'phone' => 'nullable|string|max:80', 'source' => 'nullable|string|max:120']);
        $action->execute((int) $teamId, auth()->id(), ['kind' => $this->kind, 'name' => $this->name, 'email' => $this->email, 'phone' => $this->phone, 'source' => $this->source]);
        $this->reset(['name', 'email', 'phone']);
        $this->dispatch('lead-captured');
    }

    public function render(): View
    {
        return app('view')->make('crm-lead-capture-livewire::capture-form');
    }
}
