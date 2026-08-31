<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Commands\BackupTeam;
use App\Console\Commands\CloneTeam;
use App\Console\Commands\ImportTeam;
use App\Console\Commands\MakeModuleCommand;
use App\Console\Commands\ModuleAutoloadCommand;
use App\Console\Commands\ModuleCommand;
use App\Console\Commands\NotifyOverdueTasks;
use App\Console\Commands\PublishScheduledPosts;
use App\Console\Commands\RestoreTeam;
use App\Console\Commands\SendReminders;
use App\Console\Commands\UpdatePostAnalytics;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Task;
use App\Models\Team;
use App\Modules\ModuleManager;
use App\Modules\ModuleServiceProvider;
use App\Observers\AuditObserver;
use App\Support\SsoLogoutState;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Console\WipeCommand;
use Illuminate\Foundation\Console\KeyGenerateCommand;
use Illuminate\Foundation\Console\PackageDiscoverCommand;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Liberu\Foundation\ModuleManager\ModuleDiscovery;
use Liberu\Foundation\ModuleManager\ModuleManagerServiceProvider;
use Liberu\Foundation\ModuleManager\ModuleRegistry;
use Liberu\Foundation\Observability\ObservabilityServiceProvider;
use Liberu\Foundation\Search\SearchServiceProvider;
use Liberu\Foundation\Settings\SettingsServiceProvider;
use Liberu\Foundation\Theme\Providers\ThemeServiceProvider;
use Symfony\Component\Console\Input\InputOption;

class AppServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FreshCommand::class,
                InstallCommand::class,
                KeyGenerateCommand::class,
                MigrateCommand::class,
                StatusCommand::class,
                WipeCommand::class,
                PackageDiscoverCommand::class,
                SeedCommand::class,
            ]);
            $this->commands([
                BackupTeam::class,
                CloneTeam::class,
                ImportTeam::class,
                MakeModuleCommand::class,
                ModuleAutoloadCommand::class,
                ModuleCommand::class,
                NotifyOverdueTasks::class,
                PublishScheduledPosts::class,
                RestoreTeam::class,
                SendReminders::class,
                UpdatePostAnalytics::class,
            ]);
        }

        $this->app->singleton(ModuleManager::class, fn (): ModuleManager => new ModuleManager());
        // Request-scoped holder for the SSO single-logout redirect URL.
        $this->app->singleton(SsoLogoutState::class);
        $this->app->singleton(ModuleRegistry::class, fn (): ModuleRegistry => (new ModuleDiscovery())->discover([base_path('modules')]));
        $this->app->register(ModuleServiceProvider::class);
        $this->app->register(ModuleManagerServiceProvider::class);
        $this->app->register(SettingsServiceProvider::class);
        $this->app->register(SearchServiceProvider::class);
        $this->app->register(ObservabilityServiceProvider::class);
        $this->app->register(ThemeServiceProvider::class);
    }

    public function boot(): void
    {
        Jetstream::useTeamModel(Team::class);

        if ($this->app->runningInConsole()) {
            $kernel = $this->app->make(Kernel::class);
            $artisan = \Closure::bind(fn () => $this->getArtisan(), $kernel, $kernel)();
            $command = $artisan->get('db:seed');

            if ($command !== null && ! $command->getDefinition()->hasOption('tenants')) {
                $command->getDefinition()->addOption(new InputOption('tenants', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL));
            }
        }

        // Audit core tenant models. Never observe AuditLog itself -> infinite recursion.
        foreach ([Contact::class, Deal::class, Lead::class, Opportunity::class, Task::class] as $model) {
            $model::observe(AuditObserver::class);
        }
    }
}
