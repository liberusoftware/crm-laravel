<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeApi\Providers;

use Illuminate\Support\ServiceProvider;

final class KnowledgeApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
