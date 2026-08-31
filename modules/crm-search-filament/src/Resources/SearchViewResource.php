<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CrmSearch\Models\SearchView;
use Liberu\CRM\CrmSearchFilament\Resources\Pages\CreateSearchView;
use Liberu\CRM\CrmSearchFilament\Resources\Pages\EditSearchView;
use Liberu\CRM\CrmSearchFilament\Resources\Pages\ListSearchViews;

final class SearchViewResource extends Resource
{
    protected static ?string $model = SearchView::class;

    protected static ?string $navigationLabel = 'Saved search views';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListSearchViews::route('/'), 'create' => CreateSearchView::route('/create'), 'edit' => EditSearchView::route('/{record}/edit')];
    }
}
