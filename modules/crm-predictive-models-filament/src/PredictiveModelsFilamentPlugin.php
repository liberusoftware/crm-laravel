<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource;

final class PredictiveModelsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-predictive-models';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PredictionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
