<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AutomationPackFilament\Resources\AutomationRecipeResource;

final class ListAutomationRecipes extends ListRecords
{
    protected static string $resource = AutomationRecipeResource::class;
}
