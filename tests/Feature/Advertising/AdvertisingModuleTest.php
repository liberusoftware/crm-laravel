<?php

declare(strict_types=1);

namespace Tests\Feature\Advertising;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Advertising\Actions\TransitionRecord;
use Liberu\CRM\Advertising\Actions\UpsertRecord;
use Tests\TestCase;

final class AdvertisingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_advertising_records_support_platform_metadata_and_lifecycle(): void
    {
        $record = app(UpsertRecord::class)->execute(11, ['kind' => 'campaign', 'name' => 'Spring', 'platform' => 'metaads', 'payload' => ['budget' => 1000]]);
        $record = app(TransitionRecord::class)->execute(11, $record->id, 'active');
        self::assertSame('active', $record->status);
        self::assertSame('metaads', $record->platform);
    }
}
