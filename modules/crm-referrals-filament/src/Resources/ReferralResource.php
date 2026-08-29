<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Referrals\Filament\Resources\ReferralResource\Pages\CreateReferral;
use Liberu\CRM\Referrals\Filament\Resources\ReferralResource\Pages\EditReferral;
use Liberu\CRM\Referrals\Filament\Resources\ReferralResource\Pages\ListReferrals;
use Liberu\CRM\Referrals\Models\Referral;

final class ReferralResource extends Resource
{
    protected static ?string $model = Referral::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('program_id')->numeric()->required(), TextInput::make('advocate_id')->numeric(), TextInput::make('prospect_email')->email()->required(), TextInput::make('prospect_name'), Select::make('status')->options(['pending' => 'Pending', 'qualified' => 'Qualified', 'converted' => 'Converted', 'rejected' => 'Rejected'])]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('code'), TextColumn::make('prospect_email'), TextColumn::make('status')->badge(), TextColumn::make('fraud_status')->badge(), TextColumn::make('attributed_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListReferrals::route('/'), 'create' => CreateReferral::route('/create'), 'edit' => EditReferral::route('/{record}/edit')];
    }
}
