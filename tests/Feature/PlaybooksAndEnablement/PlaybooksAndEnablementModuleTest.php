<?php

declare(strict_types=1);

namespace Tests\Feature\PlaybooksAndEnablement;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\PlaybooksAndEnablement\Actions\AssignPlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Actions\CompletePlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Actions\CreatePlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookAssignment;
use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookUsage;
use Tests\TestCase;

final class PlaybooksAndEnablementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_playbook_evidence_completion_is_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $playbook = app(CreatePlaybook::class)->execute($team->id, $owner->id, ['name' => 'Discovery', 'kind' => 'script', 'steps' => [['name' => 'Confirm pain']]]);
        $assignment = app(AssignPlaybook::class)->execute($team->id, $owner->id, ['playbook_id' => $playbook->id, 'assignee_id' => $owner->id, 'checklist' => ['pain' => false]]);
        $completed = app(CompletePlaybook::class)->execute($team->id, $owner->id, $assignment->id, ['evidence' => ['call_id' => 8]]);

        self::assertSame('completed', $completed->status);
        self::assertSame(8, $completed->evidence['call_id']);
        self::assertCount(1, PlaybookAssignment::query()->where('team_id', $team->id)->get());
        self::assertCount(0, PlaybookUsage::query()->where('team_id', $other->id)->get());
    }
}
