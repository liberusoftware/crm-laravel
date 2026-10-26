<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource;

final class AccountPlanningFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'module-crm-account-planning';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AccountPlanningRecordResource::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): self
    {
        return new self();
    }
}
