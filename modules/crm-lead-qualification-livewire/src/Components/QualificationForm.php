<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\LeadQualification\Actions\CreateQualification;
use Liberu\CRM\LeadQualification\Actions\UpdateScores;
use Liberu\CRM\LeadQualification\Models\LeadQualification;
use Livewire\Component;

final class QualificationForm extends Component
{
    public string $subjectType = '';

    public int $subjectId = 0;

    public int $fitScore = 0;

    public int $engagementScore = 0;

    public ?int $qualificationId = null;

    public function save(CreateQualification $create, UpdateScores $update): void
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $this->validate(['subjectType' => 'required|string|max:160', 'subjectId' => 'required|integer|min:1', 'fitScore' => 'required|integer|between:0,100', 'engagementScore' => 'required|integer|between:0,100']);
        if ($this->qualificationId === null) {
            $qualification = $create->execute($teamId, auth()->id(), ['subject_type' => $this->subjectType, 'subject_id' => $this->subjectId, 'fit_score' => $this->fitScore, 'engagement_score' => $this->engagementScore]);
            $this->qualificationId = $qualification->getKey();
        } else {
            $qualification = LeadQualification::query()->where('team_id', $teamId)->findOrFail($this->qualificationId);
            $update->execute($qualification, auth()->id(), ['fit_score' => $this->fitScore, 'engagement_score' => $this->engagementScore]);
        }
        $this->dispatch('qualification-saved');
    }

    public function render(): View
    {
        return view('crm-lead-qualification-livewire::qualification-form');
    }
}
