<?php

declare(strict_types=1);

namespace Liberu\CRM\CampaignsFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Campaigns\Models\Campaign;
use Liberu\CRM\CampaignsFilament\Resources\CampaignResource\Pages\CreateCampaignPage;
use Liberu\CRM\CampaignsFilament\Resources\CampaignResource\Pages\ListCampaigns;

final class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(180), KeyValue::make('objectives')->json()->required(), KeyValue::make('audience')->json(), KeyValue::make('channels')->json(), KeyValue::make('assets')->json(), TextInput::make('budget')->numeric()->minValue(0)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('budget')->money('USD'), TextColumn::make('cost')->money('USD'), TextColumn::make('revenue')->money('USD'), TextColumn::make('starts_on')->date()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCampaigns::route('/'), 'create' => CreateCampaignPage::route('/create')];
    }
}
