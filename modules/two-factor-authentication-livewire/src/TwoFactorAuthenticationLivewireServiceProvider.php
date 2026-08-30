<?php

declare(strict_types=1);

namespace Liberu\Foundation\TwoFactorAuthenticationLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class TwoFactorAuthenticationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'two-factor-authentication-livewire');
        Livewire::component('two-factor-authentication-livewire-overview', Liberu\Foundation\TwoFactorAuthenticationLivewire\Livewire\Overview::class);
    }
}
