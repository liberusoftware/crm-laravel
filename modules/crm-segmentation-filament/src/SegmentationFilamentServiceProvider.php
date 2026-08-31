<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Filament;

use Illuminate\Support\ServiceProvider;

final class SegmentationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SegmentationFilamentPlugin::class);
    }
}
