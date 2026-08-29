<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\Referrals\Filament\Resources\ReferralResource;

final class ReferralsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-referrals';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReferralResource::class]);
    }

    public function boot(Panel $panel): void {}
}
