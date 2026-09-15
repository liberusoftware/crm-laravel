<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_reports_application_and_database_status(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'checks' => [
                    'application' => 'ok',
                    'database' => 'ok',
                ],
            ]);
    }
}
