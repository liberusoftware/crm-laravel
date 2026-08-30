<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotesApi\Providers;

use Illuminate\Support\ServiceProvider;

final class ProposalsAndQuotesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
