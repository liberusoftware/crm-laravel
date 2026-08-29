<?php

declare(strict_types=1);

namespace Tests\Feature\AccountPlanning;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\AccountPlanning\Actions\UpsertRecord;
use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;
use Tests\TestCase;

final class AccountPlanningModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_planning_records_are_scoped_and_validated(): void
    {
        $record = app(UpsertRecord::class)->execute(11, ['kind' => 'account_plan', 'name' => 'Northwind plan']);
        self::assertSame('draft', $record->status);
        self::assertCount(1, AccountPlanningRecord::query()->forTeam(11)->get());
        self::assertCount(0, AccountPlanningRecord::query()->forTeam(12)->get());
    }
}
