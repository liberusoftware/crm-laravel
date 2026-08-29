<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\Projects\Filament\Resources\ProjectResource;

final class ProjectsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-projects';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ProjectResource::class]);
    }

    public function boot(Panel $panel): void {}
}
