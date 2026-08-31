<?php

declare(strict_types=1);

namespace Liberu\CRM\DocumentsFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Documents\Models\CrmDocument;
use Liberu\CRM\DocumentsFilament\Resources\Pages\CreateCrmDocument;
use Liberu\CRM\DocumentsFilament\Resources\Pages\EditCrmDocument;
use Liberu\CRM\DocumentsFilament\Resources\Pages\ListCrmDocuments;

final class CrmDocumentResource extends Resource
{
    protected static ?string $model = CrmDocument::class;

    protected static ?string $navigationLabel = 'CRM Documents';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListCrmDocuments::route('/'), 'create' => CreateCrmDocument::route('/create'), 'edit' => EditCrmDocument::route('/{record}/edit')];
    }
}
