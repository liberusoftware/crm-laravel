<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\Segmentation\Filament\Resources\AudienceResource;

final class SegmentationFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-segmentation';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AudienceResource::class]);
    }

    public function boot(Panel $panel): void {}
}
