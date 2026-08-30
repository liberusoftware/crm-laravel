<?php

declare(strict_types=1);

namespace Tests\Feature\AutomationPack;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\AutomationPack\Actions\ApproveAutomationRecipe;
use Liberu\CRM\AutomationPack\Actions\CreateAutomationRecipe;
use Liberu\CRM\AutomationPack\Actions\EnrollSubject;
use Liberu\CRM\AutomationPack\Actions\PublishAutomationRecipe;
use Liberu\CRM\AutomationPack\Actions\SimulateAutomationRecipe;
use Tests\TestCase;

final class AutomationPackModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_version_simulation_and_approval_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $recipe = app(CreateAutomationRecipe::class)->execute($team->id, $owner->id, ['name' => 'Follow up', 'triggers' => ['event' => 'lead.created'], 'conditions' => [['field' => 'score', 'gte' => 50]], 'actions' => [['type' => 'create_task', 'title' => 'Call']], 'approval_required' => true]);
        $published = app(PublishAutomationRecipe::class)->execute($team->id, $owner->id, $recipe);
        $run = app(SimulateAutomationRecipe::class)->execute($team->id, $owner->id, $published, ['subject_key' => 'lead-1']);
        $approval = app(ApproveAutomationRecipe::class)->execute($team->id, $owner->id, $published, 'approved');
        $enrollment = app(EnrollSubject::class)->execute($team->id, $owner->id, $published, ['subject_key' => 'lead-1']);
        $this->assertSame(2, $published->version);
        $this->assertSame('completed', $run->status);
        $this->assertSame('approved', $approval->status);
        $this->assertSame('enrolled', $enrollment->status);
        $this->assertSame('active', $published->fresh()->status);
    }
}
