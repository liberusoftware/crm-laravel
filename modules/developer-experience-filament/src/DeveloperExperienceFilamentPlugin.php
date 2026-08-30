<?php

declare(strict_types=1);

namespace Liberu\Foundation\DeveloperExperienceFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\DeveloperExperienceFilament\Pages\Overview;

final class DeveloperExperienceFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'developer-experience-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
