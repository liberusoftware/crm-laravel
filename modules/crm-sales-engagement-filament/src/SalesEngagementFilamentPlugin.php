<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource;

final class SalesEngagementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-sales-engagement';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SequenceResource::class]);
    }

    public function boot(Panel $panel): void {}
}
