<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\AccountBasedMarketingFilament\Resources\AccountBasedMarketingRecordResource;

final class AccountBasedMarketingFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'module-crm-account-based-marketing';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AccountBasedMarketingRecordResource::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): self
    {
        return new self();
    }
}
