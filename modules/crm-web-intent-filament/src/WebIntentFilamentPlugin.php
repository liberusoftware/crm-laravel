<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\WebIntent\Filament\Resources\WebIntentAlertResource;
use Liberu\CRM\WebIntent\Filament\Resources\WebIntentVisitResource;

final class WebIntentFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-web-intent';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([WebIntentVisitResource::class, WebIntentAlertResource::class]);
    }

    public function boot(Panel $panel): void {}
}
