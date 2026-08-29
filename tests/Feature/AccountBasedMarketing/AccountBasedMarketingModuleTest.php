<?php

declare(strict_types=1);

namespace Tests\Feature\AccountBasedMarketing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\AccountBasedMarketing\Actions\TransitionRecord;
use Liberu\CRM\AccountBasedMarketing\Actions\UpsertRecord;
use Liberu\CRM\AccountBasedMarketing\Models\AccountBasedMarketingRecord;
use Liberu\CRM\AccountBasedMarketing\Queries\AccountBasedMarketingQuery;
use Tests\TestCase;

final class AccountBasedMarketingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_records_are_tenant_scoped_and_transitionable(): void
    {
        $record = app(UpsertRecord::class)->execute(101, [
            'kind' => 'target_account',
            'name' => 'Northwind',
            'payload' => ['tier' => 'one'],
        ]);
        app(UpsertRecord::class)->execute(202, [
            'kind' => 'target_account',
            'name' => 'Other tenant',
        ]);

        self::assertCount(1, app(AccountBasedMarketingQuery::class)->records(101)->get());
        self::assertSame('draft', $record->status);

        $updated = app(TransitionRecord::class)->execute(101, $record->id, 'active');

        self::assertSame('active', $updated->status);
        self::assertDatabaseHas('crm_abm_records', ['id' => $record->id, 'team_id' => 101, 'status' => 'active']);
    }

    public function test_terminal_records_cannot_be_reopened(): void
    {
        $record = AccountBasedMarketingRecord::query()->create([
            'team_id' => 101,
            'kind' => 'measurement',
            'name' => 'Q3 measurement',
            'status' => 'completed',
        ]);

        $this->expectException(\DomainException::class);
        app(TransitionRecord::class)->execute(101, $record->id, 'active');
    }
}
