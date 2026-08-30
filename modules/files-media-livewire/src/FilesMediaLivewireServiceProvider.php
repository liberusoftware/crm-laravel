<?php

declare(strict_types=1);

namespace Liberu\Foundation\FilesMediaLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class FilesMediaLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'files-media-livewire');
        Livewire::component('files-media-livewire-overview', Liberu\Foundation\FilesMediaLivewire\Livewire\Overview::class);
    }
}
