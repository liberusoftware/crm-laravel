<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Filament;

use Illuminate\Support\ServiceProvider;

final class ProposalsAndQuotesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProposalsAndQuotesFilamentPlugin::class);
    }
}
