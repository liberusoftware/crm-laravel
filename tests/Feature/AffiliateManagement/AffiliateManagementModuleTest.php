<?php

declare(strict_types=1);

namespace Tests\Feature\AffiliateManagement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\AffiliateManagement\Actions\ApplyAffiliate;
use Liberu\CRM\AffiliateManagement\Actions\ApproveAffiliate;
use Liberu\CRM\AffiliateManagement\Actions\CreateAffiliateLink;
use Liberu\CRM\AffiliateManagement\Actions\RecordAffiliateEvent;
use Tests\TestCase;

final class AffiliateManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_affiliate_link_click_and_conversion_commission_are_team_scoped(): void
    {
        $affiliate = app(ApplyAffiliate::class)->execute(1101, ['name' => 'Partner', 'email' => 'partner@example.test']);
        $affiliate = app(ApproveAffiliate::class)->execute(1101, $affiliate);
        $link = app(CreateAffiliateLink::class)->execute(1101, $affiliate, ['code' => 'PARTNER10', 'destination' => 'https://example.test/pricing', 'campaign' => 'spring']);
        app(RecordAffiliateEvent::class)->execute(1101, $link, ['type' => 'click', 'external_key' => 'click-1']);
        $conversion = app(RecordAffiliateEvent::class)->execute(1101, $link, ['type' => 'conversion', 'external_key' => 'order-1', 'value' => 250, 'commission_rate' => .1]);

        $this->assertSame('active', $affiliate->status);
        $this->assertSame('25.00', (string) $conversion->commission);
        $this->assertSame('pending', $conversion->status);
        $this->assertDatabaseMissing('crm_affiliates', ['team_id' => 1102]);
    }
}
