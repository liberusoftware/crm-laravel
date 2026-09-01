<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGatewayFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ChannelGateway\Models\GatewayChannel;
use Liberu\CRM\ChannelGatewayFilament\Resources\GatewayChannelResource\Pages\CreateGatewayChannel;
use Liberu\CRM\ChannelGatewayFilament\Resources\GatewayChannelResource\Pages\ListGatewayChannels;

final class GatewayChannelResource extends Resource
{
    protected static ?string $model = GatewayChannel::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->maxLength(120), Select::make('kind')->options(['email' => 'Email', 'sms' => 'SMS', 'mms' => 'MMS', 'whatsapp' => 'WhatsApp', 'web_chat' => 'Web chat', 'social' => 'Social', 'push' => 'Push'])->required(), TextInput::make('provider')->required()->maxLength(120), KeyValue::make('configuration')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('kind')->badge(), TextColumn::make('provider'), TextColumn::make('status')->badge(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListGatewayChannels::route('/'), 'create' => CreateGatewayChannel::route('/create')];
    }
}
