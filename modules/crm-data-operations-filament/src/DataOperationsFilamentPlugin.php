<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource;

final class DataOperationsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-data-operations';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([DataOperationResource::class]);
    }

    public function boot(Panel $panel): void {}
}
