<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboardingFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ClientOnboarding\Models\ClientOnboarding;
use Liberu\CRM\ClientOnboardingFilament\Resources\ClientOnboardingResource\Pages\CreateClientOnboarding;
use Liberu\CRM\ClientOnboardingFilament\Resources\ClientOnboardingResource\Pages\ListClientOnboardings;

final class ClientOnboardingResource extends Resource
{
    protected static ?string $model = ClientOnboarding::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('client_key')->required()->maxLength(180), KeyValue::make('intake')->json()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('client_key')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('health')->suffix('%')->sortable(), TextColumn::make('target_launch_on')->date(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListClientOnboardings::route('/'), 'create' => CreateClientOnboarding::route('/create')];
    }
}
