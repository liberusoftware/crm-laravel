<?php

declare(strict_types=1);

namespace Tests\Feature\ChannelSales;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ChannelSales\Actions\AdvanceChannelOpportunity;
use Liberu\CRM\ChannelSales\Actions\RegisterChannelOpportunity;
use Tests\TestCase;

final class ChannelSalesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_pipeline_handoff_commission_and_forecast_are_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $o = app(RegisterChannelOpportunity::class)->execute($t->id, $u->id, 'partner-1', 'opp-1', 1000, 10, ['tier' => 'reseller']);
        $event = app(AdvanceChannelOpportunity::class)->execute($t->id, $u->id, $o, 'won', 'handed_off');
        $this->assertSame('won', $o->fresh()->stage);
        $this->assertSame(100.0, $event->commission);
        $this->assertSame('handed_off', $o->fresh()->handoff_status);
    }
}
