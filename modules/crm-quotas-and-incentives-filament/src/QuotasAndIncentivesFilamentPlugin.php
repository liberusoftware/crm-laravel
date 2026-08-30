<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\QuotasAndIncentives\Filament\Resources\QuotaResource;

final class QuotasAndIncentivesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-quotas-and-incentives';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([QuotaResource::class]);
    }

    public function boot(Panel $panel): void {}
}
