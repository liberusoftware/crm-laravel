<?php

declare(strict_types=1);

namespace Liberu\CRM\EnrichmentFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Enrichment\Models\EnrichmentProfile;
use Liberu\CRM\EnrichmentFilament\Resources\Pages\CreateEnrichmentProfile;
use Liberu\CRM\EnrichmentFilament\Resources\Pages\EditEnrichmentProfile;
use Liberu\CRM\EnrichmentFilament\Resources\Pages\ListEnrichmentProfiles;

final class EnrichmentProfileResource extends Resource
{
    protected static ?string $model = EnrichmentProfile::class;

    protected static ?string $navigationLabel = 'Enrichment';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListEnrichmentProfiles::route('/'), 'create' => CreateEnrichmentProfile::route('/create'), 'edit' => EditEnrichmentProfile::route('/{record}/edit')];
    }
}
