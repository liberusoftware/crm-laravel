<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversationFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;
use Liberu\CRM\AIReceptionAndConversationFilament\Resources\ReceptionAgentResource\Pages\CreateReceptionAgentPage;
use Liberu\CRM\AIReceptionAndConversationFilament\Resources\ReceptionAgentResource\Pages\ListReceptionAgents;

final class ReceptionAgentResource extends Resource
{
    protected static ?string $model = ReceptionAgent::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(180), Select::make('channel')->options(['chat' => 'Chat', 'voice' => 'Voice'])->required(), KeyValue::make('knowledge')->json(), KeyValue::make('tools')->json(), KeyValue::make('policy')->json(), Toggle::make('requires_human_approval')->default(true)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('channel')->badge(), TextColumn::make('status')->badge(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListReceptionAgents::route('/'), 'create' => CreateReceptionAgentPage::route('/create')];
    }
}
