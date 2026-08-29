<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource;

final class SaasPackagingFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-saas-packaging';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SubscriptionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
