<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SalesEngagement\Console\Commands\RunEngagementSequences;

final class SalesEngagementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([RunEngagementSequences::class]);
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('crm:run-engagement-sequences')->everyMinute()->withoutOverlapping();
        });
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
