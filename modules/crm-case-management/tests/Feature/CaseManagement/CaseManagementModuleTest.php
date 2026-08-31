<?php

declare(strict_types=1);

namespace Tests\Feature\CaseManagement;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\CaseManagement\Actions\OpenCase;
use Liberu\CRM\CaseManagement\Actions\TransitionCase;
use Tests\TestCase;

final class CaseManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_case_hierarchy_entitlement_escalation_and_audit_are_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $parent = app(OpenCase::class)->execute($t->id, $u->id, ['case_key' => 'case-1', 'subject' => 'Outage', 'priority' => 'high', 'entitlement' => ['response_minutes' => 60]]);
        $child = app(OpenCase::class)->execute($t->id, $u->id, ['case_key' => 'case-1-1', 'parent_id' => $parent->id, 'subject' => 'API issue']);
        $updated = app(TransitionCase::class)->execute($t->id, $u->id, $child, 'escalated');
        $this->assertSame($parent->id, $child->parent_id);
        $this->assertSame(1, $updated->escalation_level);
        $this->assertDatabaseHas('crm_case_audits', ['case_id' => $child->id, 'event' => 'status_changed']);
    }
}
