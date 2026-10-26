<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CustomerDataPlatform\Models\CdpProfile;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\Pages\CreateCdpProfile;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\Pages\EditCdpProfile;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\Pages\ListCdpProfiles;

final class CdpProfileResource extends Resource
{
    protected static ?string $model = CdpProfile::class;

    protected static ?string $navigationLabel = 'Unified profiles';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('profile_key')->label('Profile key')->helperText('Stable identifier used to unify this person across systems.')->required()->maxLength(160),
            KeyValue::make('attributes')->label('Profile attributes')->keyLabel('Attribute')->valueLabel('Value')->json()->columnSpanFull(),
            Toggle::make('consent.analytics')->label('Analytics consent'),
            Toggle::make('consent.marketing')->label('Marketing consent'),
            Toggle::make('consent.personalization')->label('Personalization consent'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('profile_key')->label('Profile')->searchable()->sortable(),
            TextColumn::make('consent.analytics')->label('Analytics')->formatStateUsing(static fn (mixed $state): string => (bool) $state ? 'Granted' : 'Not granted'),
            TextColumn::make('consent.marketing')->label('Marketing')->formatStateUsing(static fn (mixed $state): string => (bool) $state ? 'Granted' : 'Not granted'),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListCdpProfiles::route('/'), 'create' => CreateCdpProfile::route('/create'), 'edit' => EditCdpProfile::route('/{record}/edit')];
    }
}
