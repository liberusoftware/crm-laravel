<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AutomationPack\Models\AutomationRecipe;
use Liberu\CRM\AutomationPackFilament\Resources\Pages\CreateAutomationRecipe;
use Liberu\CRM\AutomationPackFilament\Resources\Pages\EditAutomationRecipe;
use Liberu\CRM\AutomationPackFilament\Resources\Pages\ListAutomationRecipes;

final class AutomationRecipeResource extends Resource
{
    protected static ?string $model = AutomationRecipe::class;

    protected static ?string $navigationLabel = 'Automation recipes';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListAutomationRecipes::route('/'), 'create' => CreateAutomationRecipe::route('/create'), 'edit' => EditAutomationRecipe::route('/{record}/edit')];
    }
}
