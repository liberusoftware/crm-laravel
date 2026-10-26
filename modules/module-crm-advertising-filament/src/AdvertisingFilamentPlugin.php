<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\AdvertisingFilament\Resources\AdvertisingRecordResource;

final class AdvertisingFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'module-crm-advertising';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AdvertisingRecordResource::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): self
    {
        return new self();
    }
}
