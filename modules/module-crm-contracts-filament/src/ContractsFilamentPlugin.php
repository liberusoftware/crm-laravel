<?php

declare(strict_types=1);

namespace Liberu\CRM\ContractsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ContractsFilament\Resources\ContractResource;

final class ContractsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-contracts';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ContractResource::class]);
    }

    public function boot(Panel $panel): void {}
}
