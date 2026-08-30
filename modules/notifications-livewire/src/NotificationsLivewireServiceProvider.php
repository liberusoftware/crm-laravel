<?php

declare(strict_types=1);

namespace Liberu\Foundation\NotificationsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class NotificationsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notifications-livewire');
        Livewire::component('notifications-livewire-overview', Liberu\Foundation\NotificationsLivewire\Livewire\Overview::class);
    }
}
