<?php

declare(strict_types=1);

namespace Tests\Feature\CRMCore;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Core\Actions\AddNote;
use Liberu\CRM\Core\Actions\CreateRecord;
use Liberu\CRM\Core\Actions\CreateRelationship;
use Liberu\CRM\Core\Actions\CreateTag;
use Liberu\CRM\Core\Actions\MergeRecords;
use Liberu\CRM\Core\Actions\TagRecord;
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

    public function test_record_owners_must_belong_to_the_team_on_create_and_update(): void
    {
        $team = Team::factory()->create();
        $outsider = User::factory()->create();

        $this->expectException(ValidationException::class);
        app(CreateRecord::class)->execute('contact', $team->id, 'Outsider', [], $outsider->id);
    }

    public function test_record_owner_updates_reject_cross_team_users(): void
    {
        $team = Team::factory()->create();
        $outsider = User::factory()->create();
        $record = app(CreateRecord::class)->execute('contact', $team->id, 'Ada');

        $this->expectException(ValidationException::class);
        app(UpdateRecord::class)->execute($record, ['owner_id' => $outsider->id]);
    }

    public function test_record_updates_reject_unknown_statuses(): void
    {
        $record = app(CreateRecord::class)->execute('contact', 1, 'Ada');

        $this->expectException(\InvalidArgumentException::class);
        app(UpdateRecord::class)->execute($record, ['status' => 'won']);
    }

    public function test_record_merge_preserves_related_crm_data_and_removes_duplicate(): void
    {
        $create = app(CreateRecord::class);
        $survivor = $create->execute('contact', 1, 'Ada', ['email' => 'ada@example.test']);
        $duplicate = $create->execute('contact', 1, 'Ada L.', ['phone' => '+1 555 0100']);
        $related = $create->execute('organization', 1, 'Analytical Engines');
        app(AddNote::class)->execute($duplicate, 'Imported note', 11);
        $tag = app(CreateTag::class)->execute(1, 'VIP');
        app(TagRecord::class)->execute($duplicate, $tag);
        app(CreateRelationship::class)->execute($duplicate, $related, 'works_for');

        $merged = app(MergeRecords::class)->execute($survivor, $duplicate, 11);

        self::assertSame(['phone' => '+1 555 0100', 'email' => 'ada@example.test'], $merged->data);
        self::assertDatabaseMissing('crm_core_records', ['id' => $duplicate->id]);
        self::assertSame(1, $merged->notes()->count());
        self::assertSame(1, $merged->tags()->count());
        self::assertSame(1, $merged->outgoingRelationships()->count());
        self::assertSame($related->id, $merged->outgoingRelationships()->firstOrFail()->to_id);
        self::assertSame('record.merged', $merged->timeline()->firstOrFail()->event_type);
    }
}
