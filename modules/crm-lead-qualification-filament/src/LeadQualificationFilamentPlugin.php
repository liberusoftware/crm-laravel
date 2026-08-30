<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource;
use Liberu\CRM\LeadQualification\Filament\Resources\QualificationFrameworkResource;

final class LeadQualificationFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-lead-qualification';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([LeadQualificationResource::class, QualificationFrameworkResource::class]);
    }

    public function boot(Panel $panel): void {}
}
