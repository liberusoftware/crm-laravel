<?php

declare(strict_types=1);

namespace Tests\Feature\SalesPipelines;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesPipelines\Actions\CloseOpportunity;
use Liberu\CRM\SalesPipelines\Actions\CreateOpportunity;
use Liberu\CRM\SalesPipelines\Actions\CreatePipeline;
use Liberu\CRM\SalesPipelines\Actions\CreateStage;
use Liberu\CRM\SalesPipelines\Actions\MoveOpportunity;
use Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource;
use Liberu\CRM\SalesPipelines\Models\Opportunity;
use Liberu\CRM\SalesPipelines\Models\StageHistory;
use Liberu\CRM\SalesPipelines\Queries\PipelineQuery;
use Tests\TestCase;

final class SalesPipelinesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipeline_opportunity_stage_history_and_loss_reason_lifecycle(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $pipeline = app(CreatePipeline::class)->execute($team->id, $owner->id, ['name' => 'New business']);
        $qualification = app(CreateStage::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'name' => 'Qualification', 'position' => 1, 'probability' => 25]);
        $proposal = app(CreateStage::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'name' => 'Proposal', 'position' => 2, 'probability' => 60]);
        $opportunity = app(CreateOpportunity::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'stage_id' => $qualification->id, 'name' => 'CRM expansion', 'value' => 10000, 'products' => ['crm']]);
        app(MoveOpportunity::class)->execute($team->id, $owner->id, $opportunity->id, ['stage_id' => $proposal->id]);
        app(CloseOpportunity::class)->execute($team->id, $owner->id, $opportunity->id, 'lost', 'Budget deferred');

        self::assertSame('lost', $opportunity->fresh()->status);
        self::assertSame('Budget deferred', $opportunity->fresh()->loss_reason);
        self::assertSame(1, StageHistory::query()->where('opportunity_id', $opportunity->id)->count());
        self::assertSame(1, Opportunity::query()->where('team_id', $team->id)->count());
    }

    public function test_opportunity_filament_resource_exposes_create_edit_and_list_pages(): void
    {
        self::assertSame(['index', 'create', 'edit'], array_keys(OpportunityResource::getPages()));
    }

    public function test_rotting_opportunities_use_stage_thresholds_and_team_scope(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $otherTeam = Team::factory()->create(['user_id' => $owner->id]);
        $pipeline = app(CreatePipeline::class)->execute($team->id, $owner->id, ['name' => 'Retention']);
        $stage = app(CreateStage::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'name' => 'Review', 'position' => 1, 'probability' => 50, 'rotting_days' => 7]);
        $old = app(CreateOpportunity::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'stage_id' => $stage->id, 'name' => 'Old deal', 'value' => 100]);
        $old->update(['last_stage_at' => now()->subDays(8)]);
        $recent = app(CreateOpportunity::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'stage_id' => $stage->id, 'name' => 'Recent deal', 'value' => 100]);
        $recent->update(['last_stage_at' => now()->subDays(6)]);
        $closed = app(CreateOpportunity::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'stage_id' => $stage->id, 'name' => 'Won deal', 'value' => 100]);
        $closed->update(['last_stage_at' => now()->subDays(20), 'status' => 'won']);

        $otherPipeline = app(CreatePipeline::class)->execute($otherTeam->id, $owner->id, ['name' => 'Other']);
        $otherStage = app(CreateStage::class)->execute($otherTeam->id, $owner->id, ['pipeline_id' => $otherPipeline->id, 'name' => 'Review', 'position' => 1, 'rotting_days' => 1]);
        $foreign = app(CreateOpportunity::class)->execute($otherTeam->id, $owner->id, ['pipeline_id' => $otherPipeline->id, 'stage_id' => $otherStage->id, 'name' => 'Foreign deal', 'value' => 100]);
        $foreign->update(['last_stage_at' => now()->subDays(20)]);

        self::assertSame([$old->id], app(PipelineQuery::class)->rottingOpportunities($team->id)->pluck('id')->all());

        $owner->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($owner)->getJson('/api/v1/crm/sales-pipelines/opportunities/rotting')
            ->assertOk()->assertJsonPath('data.data.0.id', $old->id);
    }

    public function test_closed_opportunities_cannot_be_moved_or_closed_again(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $pipeline = app(CreatePipeline::class)->execute($team->id, $owner->id, ['name' => 'New business']);
        $first = app(CreateStage::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'name' => 'Qualification', 'position' => 1]);
        $second = app(CreateStage::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'name' => 'Proposal', 'position' => 2]);
        $opportunity = app(CreateOpportunity::class)->execute($team->id, $owner->id, ['pipeline_id' => $pipeline->id, 'stage_id' => $first->id, 'name' => 'Renewal']);
        app(CloseOpportunity::class)->execute($team->id, $owner->id, $opportunity->id, 'won');

        $this->expectException(ValidationException::class);
        app(MoveOpportunity::class)->execute($team->id, $owner->id, $opportunity->id, ['stage_id' => $second->id]);
    }
}
