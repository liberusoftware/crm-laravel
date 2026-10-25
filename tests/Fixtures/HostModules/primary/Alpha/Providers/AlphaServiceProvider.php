<?php

namespace Tests\Fixtures\HostModules\primary\Alpha\Providers;

use Illuminate\Support\ServiceProvider;

final class AlphaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->instance('host.fixture.registered', true);
    }
}
