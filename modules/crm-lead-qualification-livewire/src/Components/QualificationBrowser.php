<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\LeadQualification\Models\LeadQualification;
use Livewire\Component;
use Livewire\WithPagination;

final class QualificationBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $stage = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $qualifications = LeadQualification::query()->where('team_id', $teamId)->when($this->search !== '', fn ($query) => $query->where('subject_type', 'like', '%'.addcslashes($this->search, '%_').'%'))->when($this->status !== '', fn ($query) => $query->where('qualification_status', $this->status))->when($this->stage !== '', fn ($query) => $query->where('lifecycle_stage', $this->stage))->latest()->paginate(25);

        return view('crm-lead-qualification-livewire::qualification-browser', ['qualifications' => $qualifications]);
    }
}
