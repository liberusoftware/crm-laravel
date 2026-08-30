<?php

declare(strict_types=1);

namespace Liberu\Foundation\ActivityCommentsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ActivityCommentsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'activity-comments-livewire');
        Livewire::component('activity-comments-livewire-overview', Liberu\Foundation\ActivityCommentsLivewire\Livewire\Overview::class);
    }
}
