<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\CustomerDataModel\Filament\Resources\ObjectDefinitionResource;

final class CustomerDataModelFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-customer-data-model';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ObjectDefinitionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
