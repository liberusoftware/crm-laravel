<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource\Pages\CreateWorkQueue;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource\Pages\EditWorkQueue;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource\Pages\ListWorkQueues;
use Liberu\CRM\WorkManagement\Models\WorkQueue;

final class WorkQueueResource extends Resource
{
    protected static ?string $model = WorkQueue::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(160), Textarea::make('description')->maxLength(500), Select::make('status')->options(['active' => 'Active', 'paused' => 'Paused', 'archived' => 'Archived'])->required(), Textarea::make('rules')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListWorkQueues::route('/'), 'create' => CreateWorkQueue::route('/create'), 'edit' => EditWorkQueue::route('/{record}/edit')];
    }
}
