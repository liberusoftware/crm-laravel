<?php

declare(strict_types=1);

namespace Tests\Feature\UsageWallet;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\TerritoriesAndOwnership\Actions\AssignOwner;
use Liberu\CRM\TerritoriesAndOwnership\Actions\CreateCoverage;
use Liberu\CRM\TerritoriesAndOwnership\Actions\UpsertTerritoryRule;
use Liberu\CRM\TerritoriesAndOwnership\Models\OwnershipHistory;
use Liberu\CRM\TerritoriesAndOwnership\Models\TerritoryRule;
use Tests\TestCase;

final class TerritoriesAndOwnershipModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_rules_and_ownership_history_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $rule = app(UpsertTerritoryRule::class)->execute($team->id, $owner->id, ['name' => 'North', 'book_of_business' => 'Enterprise', 'members' => [$owner->id], 'criteria' => ['region' => 'north'], 'capacity' => 5]);
        $history = app(AssignOwner::class)->execute($team->id, $owner->id, 'lead', 12, null, $owner->id, 'round_robin');
        self::assertSame('North', $rule->getAttribute('name'));
        self::assertSame($team->id, $history->getAttribute('team_id'));
        self::assertSame(1, TerritoryRule::query()->where('team_id', $team->id)->count());
        self::assertSame(1, OwnershipHistory::query()->where('team_id', $team->id)->count());
        self::assertSame(0, TerritoryRule::query()->where('team_id', $other->id)->count());
    }

    public function test_mutations_ignore_control_fields_and_reject_foreign_users(): void
    {
        $owner = User::factory()->create();
        $foreign = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $this->expectException(ValidationException::class);
        app(UpsertTerritoryRule::class)->execute($team->id, $owner->id, [
            'name' => 'North',
            'members' => [$foreign->id],
            'team_id' => 999999,
            'round_robin_cursor' => 42,
        ]);
    }

    public function test_ownership_and_coverage_require_team_membership_and_valid_windows(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $foreign = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $team->users()->attach($member->id, ['role' => 'manager']);
        $rule = app(UpsertTerritoryRule::class)->execute($team->id, $owner->id, ['name' => 'North', 'members' => [$owner->id, $member->id]]);

        $this->expectException(ValidationException::class);
        app(AssignOwner::class)->execute($team->id, $owner->id, 'lead', 12, null, $foreign->id, 'manual');

        $coverage = app(CreateCoverage::class)->execute($team->id, $owner->id, [
            'rule_id' => $rule->id,
            'covered_user_id' => $member->id,
            'substitute_user_id' => $owner->id,
            'starts_at' => '2026-09-01 09:00:00',
            'ends_at' => '2026-09-02 09:00:00',
        ]);

        self::assertSame($team->id, $coverage->getAttribute('team_id'));
    }
}
