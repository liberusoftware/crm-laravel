<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\DealRegistration\Models\DealRegistration;
use Liberu\CRM\DealRegistrationFilament\Resources\Pages\CreateDealRegistration;
use Liberu\CRM\DealRegistrationFilament\Resources\Pages\EditDealRegistration;
use Liberu\CRM\DealRegistrationFilament\Resources\Pages\ListDealRegistrations;

final class DealRegistrationResource extends Resource
{
    protected static ?string $model = DealRegistration::class;

    protected static ?string $navigationLabel = 'Deal registration';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListDealRegistrations::route('/'), 'create' => CreateDealRegistration::route('/create'), 'edit' => EditDealRegistration::route('/{record}/edit')];
    }
}
