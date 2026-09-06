<?php

declare(strict_types=1);

namespace Liberu\CRM\CollaborationFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Collaboration\Models\CollaborationWork;
use Liberu\CRM\CollaborationFilament\Resources\CollaborationWorkResource\Pages\CreateCollaborationWork;
use Liberu\CRM\CollaborationFilament\Resources\CollaborationWorkResource\Pages\ListCollaborationWork;

final class CollaborationWorkResource extends Resource
{
    protected static ?string $model = CollaborationWork::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('queue_key')->required()->maxLength(120), TextInput::make('subject_key')->required()->maxLength(180), TextInput::make('assignee_key')->maxLength(180), KeyValue::make('metadata')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('queue_key')->searchable(), TextColumn::make('subject_key')->searchable(), TextColumn::make('assignee_key'), TextColumn::make('status')->badge(), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCollaborationWork::route('/'), 'create' => CreateCollaborationWork::route('/create')];
    }
}
