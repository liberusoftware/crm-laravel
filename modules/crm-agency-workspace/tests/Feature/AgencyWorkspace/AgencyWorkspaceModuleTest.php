<?php

declare(strict_types=1);

namespace Tests\Feature\AgencyWorkspace;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\AgencyWorkspace\Actions\CreateAgencyAccount;
use Liberu\CRM\AgencyWorkspace\Actions\GrantAgencyAccess;
use Liberu\CRM\AgencyWorkspace\Actions\UpdateAgencyUsage;
use Tests\TestCase;

final class AgencyWorkspaceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_hierarchy_access_usage_and_audit_are_team_scoped(): void
    {
        $agency = app(CreateAgencyAccount::class)->execute(1001, 5, ['name' => 'Agency', 'account_type' => 'agency', 'branding' => ['logo' => 'agency.svg']]);
        $client = app(CreateAgencyAccount::class)->execute(1001, 5, ['name' => 'Client', 'parent_id' => $agency->id]);
        app(GrantAgencyAccess::class)->execute(1001, 5, $client, ['user_id' => 22, 'role' => 'support', 'expires_at' => now()->addHour()]);
        $client = app(UpdateAgencyUsage::class)->execute(1001, 5, $client, ['usage' => ['seats' => 4, 'api_calls' => 12]]);

        $this->assertSame($agency->id, $client->parent_id);
        $this->assertSame(4, $client->usage_snapshot['seats']);
        $this->assertCount(2, $client->audits()->get());
        $this->assertDatabaseMissing('crm_agency_accounts', ['team_id' => 1002]);
    }
}
