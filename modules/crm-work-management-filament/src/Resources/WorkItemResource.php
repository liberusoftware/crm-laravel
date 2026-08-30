<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource\Pages\CreateWorkItem;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource\Pages\EditWorkItem;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource\Pages\ListWorkItems;
use Liberu\CRM\WorkManagement\Models\WorkItem;

final class WorkItemResource extends Resource
{
    protected static ?string $model = WorkItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(200),
            Textarea::make('description')->columnSpanFull(),
            Select::make('status')->options(['pending' => 'Pending', 'in_progress' => 'In progress', 'blocked' => 'Blocked', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])->required(),
            Select::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'])->required(),
            TextInput::make('assigned_to')->numeric()->minValue(1),
            TextInput::make('queue_id')->numeric()->minValue(1),
            TextInput::make('subject_type')->maxLength(160),
            TextInput::make('subject_id')->numeric()->minValue(1),
            DateTimePicker::make('due_at'),
            TextInput::make('recurrence')->maxLength(80),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('priority')->badge(), TextColumn::make('assigned_to'), TextColumn::make('due_at')->dateTime(), TextColumn::make('updated_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListWorkItems::route('/'), 'create' => CreateWorkItem::route('/create'), 'edit' => EditWorkItem::route('/{record}/edit')];
    }
}
