<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboardingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ClientOnboardingFilament\Resources\ClientOnboardingResource;

final class ClientOnboardingFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-client-onboarding';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ClientOnboardingResource::class]);
    }

    public function boot(Panel $panel): void {}
}
