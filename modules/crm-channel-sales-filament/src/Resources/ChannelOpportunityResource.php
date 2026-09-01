<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSalesFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ChannelSales\Models\ChannelOpportunity;
use Liberu\CRM\ChannelSalesFilament\Resources\ChannelOpportunityResource\Pages\CreateChannelOpportunity;
use Liberu\CRM\ChannelSalesFilament\Resources\ChannelOpportunityResource\Pages\ListChannelOpportunities;

final class ChannelOpportunityResource extends Resource
{
    protected static ?string $model = ChannelOpportunity::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('partner_key')->required()->maxLength(120), TextInput::make('opportunity_key')->required()->maxLength(120), TextInput::make('amount')->numeric()->minValue(0)->required(), TextInput::make('commission_rate')->numeric()->minValue(0)->maxValue(100)->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('partner_key')->searchable(), TextColumn::make('opportunity_key')->searchable(), TextColumn::make('stage')->badge(), TextColumn::make('handoff_status')->badge(), TextColumn::make('amount')->money('USD'), TextColumn::make('commission_rate')->suffix('%')]);
    }

    public static function getPages(): array
    {
        return ['index' => ListChannelOpportunities::route('/'), 'create' => CreateChannelOpportunity::route('/create')];
    }
}
