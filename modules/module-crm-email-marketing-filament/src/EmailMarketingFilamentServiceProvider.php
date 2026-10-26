<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\EmailMarketingFilament\Resources\EmailCampaignResource;

final class EmailMarketingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmailMarketingFilamentPlugin::class);
    }
}

final class EmailMarketingFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-email-marketing';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([EmailCampaignResource::class]);
    }

    public function boot(Panel $panel): void {}
}
