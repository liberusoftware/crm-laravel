<?php

declare(strict_types=1);

namespace Tests\Feature\Projects;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Projects\Actions\ChangeProjectStatus;
use Liberu\CRM\Projects\Actions\CreateProject;
use Liberu\CRM\Projects\Actions\CreateProjectTask;
use Liberu\CRM\Projects\Actions\LogProjectTime;
use Liberu\CRM\Projects\Actions\RecordProjectRisk;
use Liberu\CRM\Projects\Filament\Resources\ProjectResource;
use Liberu\CRM\Projects\Models\Project;
use Liberu\CRM\Projects\Models\ProjectTask;
use Tests\TestCase;

final class ProjectsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_resource_exposes_the_complete_filament_lifecycle(): void
    {
        self::assertSame(['index', 'create', 'edit'], array_keys(ProjectResource::getPages()));
    }

    public function test_project_delivery_status_tasks_time_and_risks_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $project = app(CreateProject::class)->execute($team->id, $owner->id, ['name' => 'Acme implementation', 'customer_id' => 42, 'starts_at' => '2026-08-26', 'ends_at' => '2026-09-30']);
        $task = app(CreateProjectTask::class)->execute($team->id, $owner->id, ['project_id' => $project->id, 'name' => 'Configure workspace']);
        app(LogProjectTime::class)->execute($team->id, $owner->id, ['project_id' => $project->id, 'task_id' => $task->id, 'hours' => 3.5, 'worked_at' => '2026-08-26']);
        app(RecordProjectRisk::class)->execute($team->id, $owner->id, ['project_id' => $project->id, 'title' => 'Data delay', 'severity' => 'high']);
        app(ChangeProjectStatus::class)->execute($team->id, $owner->id, $project->id, 'active');

        self::assertSame('active', $project->refresh()->status);
        self::assertCount(1, ProjectTask::query()->where('team_id', $team->id)->get());
        self::assertCount(0, Project::query()->where('team_id', $other->id)->get());
    }
}
