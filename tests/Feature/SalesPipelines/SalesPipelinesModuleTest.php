<?php

declare(strict_types=1);

namespace Tests\Feature\SalesPipelines;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\SalesPipelines\Actions\CloseOpportunity;
use Liberu\CRM\SalesPipelines\Actions\CreateOpportunity;
use Liberu\CRM\SalesPipelines\Actions\CreatePipeline;
use Liberu\CRM\SalesPipelines\Actions\CreateStage;
use Liberu\CRM\SalesPipelines\Actions\MoveOpportunity;
use Liberu\CRM\SalesPipelines\Models\Opportunity;
use Liberu\CRM\SalesPipelines\Models\StageHistory;
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
}
