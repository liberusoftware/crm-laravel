<?php

declare(strict_types=1);

namespace Tests\Feature\CRMCore;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Core\Actions\CreateRecord;
use Liberu\CRM\Core\Enums\RecordType;
use Liberu\CRM\Core\Models\Contact;
use Tests\TestCase;

final class CRMCoreModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_records_are_created_with_a_typed_lifecycle_entry(): void
    {
        $record = app(CreateRecord::class)->execute(RecordType::Contact->value, 1, 'Ada Lovelace', ['email' => 'ada@example.test']);

        self::assertInstanceOf(Contact::class, Contact::query()->find($record->getKey()));
        self::assertSame('contact', $record->record_type);
        self::assertSame('record.created', $record->timeline()->firstOrFail()->event_type);
    }
}
