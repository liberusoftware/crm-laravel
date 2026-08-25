<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource;

final class SalesPipelinesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-sales-pipelines';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([OpportunityResource::class]);
    }

    public function boot(Panel $panel): void {}
}
