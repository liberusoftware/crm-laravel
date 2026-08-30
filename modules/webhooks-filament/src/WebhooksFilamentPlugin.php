<?php

declare(strict_types=1);

namespace Liberu\Foundation\WebhooksFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\WebhooksFilament\Pages\Overview;

final class WebhooksFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'webhooks-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
