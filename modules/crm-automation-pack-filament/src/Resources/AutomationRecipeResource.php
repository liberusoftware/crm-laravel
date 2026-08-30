<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\AutomationPack\Models\AutomationRecipe;

final class AutomationRecipeResource extends Resource
{
    protected static ?string $model = AutomationRecipe::class;

    protected static ?string $navigationLabel = 'Automation recipes';

    public static function getPages(): array
    {
        return [];
    }
}
