<?php

declare(strict_types=1);

namespace Tests\Feature\Attribution;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Attribution\Actions\RecordConversion;
use Liberu\CRM\Attribution\Actions\RecordTouchpoint;
use Tests\TestCase;

final class AttributionModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_touchpoints_are_normalized_and_conversion_value_is_allocated(): void
    {
        $touchpoint = app(RecordTouchpoint::class)->execute(801, ['visitor_key' => 'visitor-1', 'source' => '  NEWSLETTER ', 'medium' => 'email', 'click_id' => 'click-1']);
        app(RecordTouchpoint::class)->execute(801, ['visitor_key' => 'visitor-1', 'source' => 'search', 'click_id' => 'click-2']);
        $conversion = app(RecordConversion::class)->execute(801, ['visitor_key' => 'visitor-1', 'conversion_key' => 'order-1', 'value' => 100, 'model' => 'linear']);

        $this->assertSame('newsletter', $touchpoint->source);
        $this->assertEquals(100.0, array_sum($conversion->allocations));
        $this->assertSame(2, count($conversion->allocations));
    }
}
