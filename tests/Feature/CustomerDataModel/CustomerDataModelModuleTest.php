<?php

declare(strict_types=1);

namespace Tests\Feature\CustomerDataModel;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\CustomerDataModel\Actions\PublishSchema;
use Liberu\CRM\CustomerDataModel\Contracts\SchemaValidator;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;
use Liberu\CRM\CustomerDataModel\Models\SchemaVersion;
use Tests\TestCase;

final class CustomerDataModelModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_validation_enforces_required_and_stage_fields(): void
    {
        $object = ObjectDefinition::query()->create(['team_id' => 1, 'key' => 'account', 'label' => 'Account']);
        $object->fields()->create(['key' => 'name', 'label' => 'Name', 'type' => 'text', 'is_required' => true]);
        $object->fields()->create(['key' => 'industry', 'label' => 'Industry', 'type' => 'text', 'required_stages' => ['qualified']]);

        $errors = app(SchemaValidator::class)->validate($object, [], 'qualified');

        self::assertSame(['This field is required.'], $errors['name']);
        self::assertSame(['This field is required.'], $errors['industry']);
    }

    public function test_publishing_creates_an_immutable_schema_snapshot(): void
    {
        $object = ObjectDefinition::query()->create(['team_id' => 1, 'key' => 'account', 'label' => 'Account']);
        $object->fields()->create(['key' => 'name', 'label' => 'Name', 'type' => 'text', 'position' => 1]);

        $version = app(PublishSchema::class)->execute($object, 7);

        self::assertInstanceOf(SchemaVersion::class, $version);
        self::assertSame(1, $version->version);
        self::assertSame('published', $version->status);
        self::assertSame(1, $object->refresh()->current_version);
        self::assertSame('name', $version->snapshot['fields'][0]['key']);
    }
}
