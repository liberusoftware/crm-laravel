<?php

declare(strict_types=1);

namespace Tests\Feature\Memberships;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Memberships\Actions\ChangeGrantStatus;
use Liberu\CRM\Memberships\Actions\CreatePlan;
use Liberu\CRM\Memberships\Actions\GrantAccess;
use Liberu\CRM\Memberships\Models\MembershipResource;
use Tests\TestCase;

final class MembershipsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_access_lifecycle_is_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $plan = app(CreatePlan::class)->execute($team->id, $owner->id, ['name' => 'Gold', 'interval' => 'annual', 'price' => 120, 'currency' => 'USD', 'status' => 'active']);
        $resource = MembershipResource::query()->create(['team_id' => $team->id, 'key' => 'course-1', 'name' => 'Course']);
        $grant = app(GrantAccess::class)->execute($team->id, $owner->id, $plan, ['resource_id' => $resource->id, 'member_id' => $owner->id, 'starts_at' => now(), 'renewal_reference' => 'renew-1']);
        app(ChangeGrantStatus::class)->execute($team->id, $owner->id, $grant, ['status' => 'suspended']);
        $this->assertDatabaseHas('crm_membership_grants', ['team_id' => $team->id, 'status' => 'suspended', 'renewal_reference' => 'renew-1']);
        $this->assertDatabaseMissing('crm_membership_plans', ['team_id' => $other->id, 'name' => 'Gold']);
    }
}
