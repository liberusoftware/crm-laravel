<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPaginationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_module_apis_bound_page_sizes(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $this->actingAs($user, 'sanctum');

        $endpoints = [
            '/api/v1/crm/projects',
            '/api/v1/crm/proposals-and-quotes/proposals',
            '/api/v1/crm/prospecting/prospects',
            '/api/v1/crm/quotas-and-incentives/credits',
            '/api/v1/crm/referrals/referrals',
            '/api/v1/crm/reputation-management/requests',
            '/api/v1/crm/reputation-management/reviews',
            '/api/v1/crm/resource-planning/bookings',
            '/api/v1/crm/revenue-intelligence/insights',
            '/api/v1/crm/routing/assignments',
            '/api/v1/crm/saas-packaging/usage',
            '/api/v1/crm/sales-engagement/enrollments',
            '/api/v1/crm/sales-engagement/tasks',
            '/api/v1/crm/sales-pipelines/opportunities',
            '/api/v1/crm/sales-pipelines/opportunities/rotting',
            '/api/v1/crm/sales-workspace/feed',
            '/api/v1/crm/sandbox-and-release-management/changesets',
            '/api/v1/crm/scheduling/bookings',
            '/api/v1/crm/segmentation/audiences',
            '/api/v1/crm/service-agent/cases',
            '/api/v1/crm/service-analytics/snapshots',
        ];
        foreach ($endpoints as $endpoint) {
            foreach ([1000000 => 100, -1 => 1, 0 => 1, 25 => 25] as $requested => $expected) {
                $response = $this->getJson($endpoint.'?per_page='.$requested)->assertOk();
                $pageSize = $response->json('data.per_page');
                $this->assertGreaterThanOrEqual(1, $pageSize, $endpoint);
                $this->assertLessThanOrEqual(100, $pageSize, $endpoint);
                if ($endpoint === '/api/v1/crm/projects') {
                    $response->assertJsonPath('data.per_page', $expected);
                }
            }
        }
    }
}
