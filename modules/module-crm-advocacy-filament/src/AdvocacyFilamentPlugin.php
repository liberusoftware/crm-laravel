<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\AdvocacyFilament\Resources\AdvocacyRecordResource;

final class AdvocacyFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'module-crm-advocacy';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AdvocacyRecordResource::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): self
    {
        return new self();
    }
}
