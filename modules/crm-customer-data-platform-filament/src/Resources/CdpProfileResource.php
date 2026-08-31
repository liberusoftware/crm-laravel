<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CustomerDataPlatform\Models\CdpProfile;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\Pages\CreateCdpProfile;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\Pages\EditCdpProfile;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\Pages\ListCdpProfiles;

final class CdpProfileResource extends Resource
{
    protected static ?string $model = CdpProfile::class;

    protected static ?string $navigationLabel = 'Unified profiles';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListCdpProfiles::route('/'), 'create' => CreateCdpProfile::route('/create'), 'edit' => EditCdpProfile::route('/{record}/edit')];
    }
}
