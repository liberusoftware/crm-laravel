<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CustomerSuccess\Models\SuccessCustomer;
use Liberu\CRM\CustomerSuccessFilament\Resources\Pages\CreateSuccessCustomer;
use Liberu\CRM\CustomerSuccessFilament\Resources\Pages\EditSuccessCustomer;
use Liberu\CRM\CustomerSuccessFilament\Resources\Pages\ListSuccessCustomers;

final class SuccessCustomerResource extends Resource
{
    protected static ?string $model = SuccessCustomer::class;

    protected static ?string $navigationLabel = 'Customer success';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListSuccessCustomers::route('/'), 'create' => CreateSuccessCustomer::route('/create'), 'edit' => EditSuccessCustomer::route('/{record}/edit')];
    }
}
