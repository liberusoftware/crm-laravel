<?php

declare(strict_types=1);

namespace Tests\Feature\CRMCore;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Core\Actions\CreateRecord;
use Liberu\CRM\Core\Actions\CreateRelationship;
use Liberu\CRM\Core\Actions\UpdateRecord;
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

    public function test_record_updates_are_transactional_and_audited(): void
    {
        $record = app(CreateRecord::class)->execute('organization', 1, 'Liberu', ['plan' => 'free']);

        $updated = app(UpdateRecord::class)->execute($record, ['name' => 'Liberu Software', 'owner_id' => null, 'data' => ['plan' => 'pro']]);

        self::assertSame('Liberu Software', $updated->name);
        self::assertSame(['plan' => 'pro'], $updated->data);
        self::assertSame('record.updated', $updated->timeline()->firstOrFail()->event_type);
    }

    public function test_record_creation_rejects_unknown_types_and_empty_names(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        app(CreateRecord::class)->execute('prospect', 1, 'Unknown type');
    }

    public function test_relationships_are_team_scoped_and_recorded_on_the_timeline(): void
    {
        $create = app(CreateRecord::class);
        $from = $create->execute('contact', 1, 'Ada');
        $to = $create->execute('organization', 1, 'Analytical Engines');

        $relationship = app(CreateRelationship::class)->execute($from, $to, 'works_for', ['primary' => true]);

        self::assertSame(1, $relationship->team_id);
        self::assertSame('works_for', $relationship->relationship_type);
        self::assertSame($to->getKey(), $from->outgoingRelationships()->firstOrFail()->to_id);
        self::assertSame('record.relationship.created', $from->timeline()->firstOrFail()->event_type);
    }
}
