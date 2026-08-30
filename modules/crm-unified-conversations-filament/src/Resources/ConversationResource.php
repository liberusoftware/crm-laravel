<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\UnifiedConversations\Filament\Resources\ConversationResource\Pages\ListConversations;
use Liberu\CRM\UnifiedConversations\Models\Conversation;

final class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $slug = 'conversations';

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('channel')->required(), TextInput::make('subject')]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('channel'), TextColumn::make('subject'), TextColumn::make('status'), TextColumn::make('assigned_to')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function mutateFormDataBeforeCreate(array $d): array
    {
        $d['team_id'] = (int) auth()->user()->current_team_id;

        return $d;
    }

    public static function getPages(): array
    {
        return ['index' => ListConversations::route('/')];
    }
}
