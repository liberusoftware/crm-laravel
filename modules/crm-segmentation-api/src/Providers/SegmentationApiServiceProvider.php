<?php

declare(strict_types=1);

namespace Liberu\CRM\SegmentationApi\Providers;

use Illuminate\Support\ServiceProvider;

final class SegmentationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
