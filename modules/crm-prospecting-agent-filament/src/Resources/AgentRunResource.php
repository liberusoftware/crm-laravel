<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Filament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ProspectingAgent\Filament\Resources\AgentRunResource\Pages\ListAgentRuns;
use Liberu\CRM\ProspectingAgent\Models\AgentRun;

final class AgentRunResource extends Resource
{
    protected static ?string $model = AgentRun::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), Textarea::make('targeting')->required(), Textarea::make('policy')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('status')->badge(), TextColumn::make('approved')->badge(), TextColumn::make('started_at')->dateTime(), TextColumn::make('completed_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListAgentRuns::route('/')];
    }
}
