<?php

declare(strict_types=1);

namespace Liberu\CRM\ContactCenterApi;

use Illuminate\Support\ServiceProvider;

final class ContactCenterApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
