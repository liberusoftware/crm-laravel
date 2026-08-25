<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCoursesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\LearningAndCourses\Queries\LearningQuery;
use Livewire\Component;

final class LearningPortal extends Component
{
    public function render(): View
    {
        return app('view')->make('module-crm-learning-and-courses::portal', ['courses' => app(LearningQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
