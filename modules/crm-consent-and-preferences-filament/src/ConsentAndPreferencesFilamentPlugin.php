<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource;

final class ConsentAndPreferencesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-consent-and-preferences';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ConsentRecordResource::class]);
    }

    public function boot(Panel $panel): void {}
}
