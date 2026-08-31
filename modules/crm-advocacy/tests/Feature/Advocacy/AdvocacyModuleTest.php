<?php

declare(strict_types=1);

namespace Tests\Feature\Advocacy;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Advocacy\Actions\UpsertRecord;
use Liberu\CRM\Advocacy\Models\AdvocacyRecord;
use Tests\TestCase;

final class AdvocacyModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_advocacy_records_preserve_consent_and_contact_metadata(): void
    {
        $record = app(UpsertRecord::class)->execute(11, ['kind' => 'case_study_consent', 'name' => 'Northwind consent', 'contact_id' => 42, 'payload' => ['scope' => 'public']]);
        self::assertSame(42, $record->contact_id);
        self::assertSame('public', $record->payload['scope']);
        self::assertCount(1, AdvocacyRecord::query()->forTeam(11)->get());
    }
}
