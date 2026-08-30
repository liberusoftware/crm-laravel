<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Filament\Resources;

use Filament\Actions\BulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Liberu\CRM\Activities\Filament\Resources\ActivityResource\Pages\CreateActivity;
use Liberu\CRM\Activities\Filament\Resources\ActivityResource\Pages\EditActivity;
use Liberu\CRM\Activities\Filament\Resources\ActivityResource\Pages\ListActivities;
use Liberu\CRM\Activities\Models\Activity;

final class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('kind')->options(['task' => 'Task', 'call' => 'Call', 'meeting' => 'Meeting', 'email' => 'Email'])->required(),
            TextInput::make('title')->required()->maxLength(180),
            Textarea::make('description')->columnSpanFull(),
            TextInput::make('subject_type')->maxLength(120), TextInput::make('subject_id')->numeric(),
            DateTimePicker::make('starts_at'), DateTimePicker::make('due_at'), DateTimePicker::make('ends_at'), DateTimePicker::make('reminder_at'),
            Select::make('recurrence')->options(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly']),
            TextInput::make('queue')->maxLength(80), Select::make('status')->options(['planned' => 'Planned', 'in_progress' => 'In progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('title')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('assigned_to'), TextColumn::make('due_at')->dateTime(), TextColumn::make('completed_at')->dateTime()])->bulkActions([
            BulkAction::make('complete')->requiresConfirmation()->action(function (Collection $records): void {
                DB::table('crm_activities')->whereIn('id', $records->pluck('id'))->update(['status' => 'completed', 'completed_at' => now(), 'updated_at' => now()]);
            }),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListActivities::route('/'), 'create' => CreateActivity::route('/create'), 'edit' => EditActivity::route('/{record}/edit')];
    }
}
