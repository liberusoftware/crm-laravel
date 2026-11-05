<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

final class DialerAndOutreachServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->app->afterResolving(Schedule::class, function ($schedule): void {
            $schedule->call(function (): void {
                Models\DialerList::query()->where('status', 'active')->whereIn('mode', ['power', 'progressive'])->each(function ($list): void {
                    try {
                        app(Actions\QueueDialerCall::class)->dispatch((int) $list->team_id, (int) $list->owner_id, $list);
                    } catch (\Throwable $exception) {
                        report($exception);
                    }
                });
            })->name('crm-auto-dialer')->everyMinute()->withoutOverlapping();
        });
    }
}
