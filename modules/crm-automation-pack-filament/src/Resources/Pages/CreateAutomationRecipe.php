<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\AutomationPackFilament\Resources\AutomationRecipeResource;

final class CreateAutomationRecipe extends CreateRecord
{
    protected static string $resource = AutomationRecipeResource::class;
}
