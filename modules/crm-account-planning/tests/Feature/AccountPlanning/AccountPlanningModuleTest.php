<?php

declare(strict_types=1);

namespace Tests\Feature\AccountPlanning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Laravel\Sanctum\Sanctum;
use Liberu\CRM\AccountPlanning\Actions\UpsertRecord;
use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;
use Liberu\CRM\AccountPlanningApi\AccountPlanningApiServiceProvider;
use Tests\TestCase;

final class AccountPlanningModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->register(AccountPlanningApiServiceProvider::class);
        (new AccountPlanningApiServiceProvider($this->app))->boot();
    }

    public function test_account_planning_records_are_scoped_and_validated(): void
    {
        $record = app(UpsertRecord::class)->execute(11, ['kind' => 'account_plan', 'name' => 'Northwind plan']);
        self::assertSame('draft', $record->status);
        self::assertCount(1, AccountPlanningRecord::query()->forTeam(11)->get());
        self::assertCount(0, AccountPlanningRecord::query()->forTeam(12)->get());
    }

    public function test_account_planning_rejects_invalid_kind_and_blank_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(UpsertRecord::class)->execute(11, ['kind' => 'unknown', 'name' => '']);
    }

    public function test_account_planning_api_is_explicitly_scoped_and_archives(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        Sanctum::actingAs($user);
        $record = app(UpsertRecord::class)->execute((int) $user->current_team_id, ['kind' => 'risk', 'name' => 'Renewal risk']);

        $this->getJson("/api/v1/crm/account-planning/records/{$record->id}")
            ->assertOk()
            ->assertJsonPath('data.type', 'crm-account-planning')
            ->assertJsonPath('data.attributes.name', 'Renewal risk');

        $this->deleteJson("/api/v1/crm/account-planning/records/{$record->id}")->assertNoContent();
        self::assertSame('archived', $record->refresh()->status);
    }
}
