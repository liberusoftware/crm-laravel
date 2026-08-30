<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeLivewire;

use Illuminate\Support\ServiceProvider;

final class KnowledgeLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-knowledge');
    }
}
