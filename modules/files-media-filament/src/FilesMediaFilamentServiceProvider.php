<?php

declare(strict_types=1);

namespace Liberu\Foundation\FilesMediaFilament;

use Illuminate\Support\ServiceProvider;

final class FilesMediaFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'files-media-filament');
    }
}
