<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ProductWorkspace\Models\WorkspaceProduct;
use Liberu\CRM\ProductWorkspaceFilament\Resources\Pages\CreateWorkspaceProduct;
use Liberu\CRM\ProductWorkspaceFilament\Resources\Pages\EditWorkspaceProduct;
use Liberu\CRM\ProductWorkspaceFilament\Resources\Pages\ListWorkspaceProducts;

final class WorkspaceProductResource extends Resource
{
    protected static ?string $model = WorkspaceProduct::class;

    protected static ?string $navigationLabel = 'Product workspace';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListWorkspaceProducts::route('/'), 'create' => CreateWorkspaceProduct::route('/create'), 'edit' => EditWorkspaceProduct::route('/{record}/edit')];
    }
}
