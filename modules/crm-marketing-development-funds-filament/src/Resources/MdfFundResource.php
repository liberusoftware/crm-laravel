<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFundsFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\MarketingDevelopmentFunds\Models\MdfFund;
use Liberu\CRM\MarketingDevelopmentFundsFilament\Resources\Pages\CreateMdfFund;
use Liberu\CRM\MarketingDevelopmentFundsFilament\Resources\Pages\EditMdfFund;
use Liberu\CRM\MarketingDevelopmentFundsFilament\Resources\Pages\ListMdfFunds;

final class MdfFundResource extends Resource
{
    protected static ?string $model = MdfFund::class;

    protected static ?string $navigationLabel = 'Marketing Funds';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListMdfFunds::route('/'), 'create' => CreateMdfFund::route('/create'), 'edit' => EditMdfFund::route('/{record}/edit')];
    }
}
