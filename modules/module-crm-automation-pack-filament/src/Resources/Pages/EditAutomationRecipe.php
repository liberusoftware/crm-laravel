<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\AutomationPackFilament\Resources\AutomationRecipeResource;

final class EditAutomationRecipe extends EditRecord
{
    protected static string $resource = AutomationRecipeResource::class;
}
