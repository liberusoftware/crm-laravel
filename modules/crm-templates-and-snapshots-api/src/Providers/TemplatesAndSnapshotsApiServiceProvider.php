<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshotsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class TemplatesAndSnapshotsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
