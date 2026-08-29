<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Projects\Filament\Resources\ProjectResource\Pages\CreateProject;
use Liberu\CRM\Projects\Filament\Resources\ProjectResource\Pages\EditProject;
use Liberu\CRM\Projects\Filament\Resources\ProjectResource\Pages\ListProjects;
use Liberu\CRM\Projects\Models\Project;

final class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('template_id')->numeric(), TextInput::make('name')->required(), Textarea::make('description'), TextInput::make('customer_id')->numeric(), TextInput::make('opportunity_id')->numeric(), TextInput::make('owner_id')->numeric(), DatePicker::make('starts_at'), DatePicker::make('ends_at')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('owner_id'), TextColumn::make('status')->badge(), TextColumn::make('starts_at')->date(), TextColumn::make('ends_at')->date(), TextColumn::make('client_visible')->badge()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListProjects::route('/'), 'create' => CreateProject::route('/create'), 'edit' => EditProject::route('/{record}/edit')];
    }
}
