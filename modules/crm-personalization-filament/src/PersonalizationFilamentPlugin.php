<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\Personalization\Filament\Resources\PersonalizationRuleResource;

final class PersonalizationFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-personalization';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PersonalizationRuleResource::class]);
    }

    public function boot(Panel $panel): void {}
}
