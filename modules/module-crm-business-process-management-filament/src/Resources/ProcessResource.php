<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagementFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\BusinessProcessManagement\Models\Process;
use Liberu\CRM\BusinessProcessManagementFilament\Resources\ProcessResource\Pages\CreateProcessPage;
use Liberu\CRM\BusinessProcessManagementFilament\Resources\ProcessResource\Pages\ListProcesses;

final class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(180), TextInput::make('key')->required()->maxLength(120), TextInput::make('version')->numeric()->minValue(1)->default(1), KeyValue::make('definition')->json()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('version'), TextColumn::make('updated_at')->dateTime()->sortable()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListProcesses::route('/'), 'create' => CreateProcessPage::route('/create')];
    }
}
