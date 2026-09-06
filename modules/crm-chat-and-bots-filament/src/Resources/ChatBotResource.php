<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ChatAndBots\Models\ChatBot;
use Liberu\CRM\ChatAndBotsFilament\Resources\ChatBotResource\Pages\CreateChatBotPage;
use Liberu\CRM\ChatAndBotsFilament\Resources\ChatBotResource\Pages\ListChatBots;

final class ChatBotResource extends Resource
{
    protected static ?string $model = ChatBot::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(180), KeyValue::make('playbook')->json()->required(), KeyValue::make('channels')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListChatBots::route('/'), 'create' => CreateChatBotPage::route('/create')];
    }
}
