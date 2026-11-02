<?php

declare(strict_types=1);

namespace Tests\Feature\Tenancy;

use App\Support\TenantContext;
use Composer\InstalledVersions;
use Liberu\Foundation\Tenancy\TenantContext as SharedTenantContext;
use Liberu\Foundation\Tenancy\TenantState;
use Tests\TestCase;

class SharedTenancyTest extends TestCase
{
    public function test_legacy_and_package_context_share_the_same_state(): void
    {
        TenantContext::set(42);

        $this->assertSame(42, SharedTenantContext::currentId());

        SharedTenantContext::clear();

        $this->assertNull(TenantContext::currentId());
    }

    public function test_new_worker_lifecycle_discards_the_previous_tenant(): void
    {
        TenantContext::set(42);
        $previous = app(TenantState::class);

        $this->app->forgetScopedInstances();

        $this->assertNotSame($previous, app(TenantState::class));
        $this->assertNull(TenantContext::currentId());
    }

    public function test_stancl_is_not_installed(): void
    {
        $this->assertFalse(InstalledVersions::isInstalled('stancl/tenancy'));
    }
}
