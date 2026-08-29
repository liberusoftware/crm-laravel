<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ServiceAgent\Filament\Resources\AgentCaseResource\Pages\ListAgentCases;
use Liberu\CRM\ServiceAgent\Models\AgentCase;

final class AgentCaseResource extends Resource
{
    protected static ?string $model = AgentCase::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('subject')->required(), Textarea::make('input')->required(), Select::make('status')->options(['new' => 'New', 'classified' => 'Classified', 'in_progress' => 'In progress', 'escalated' => 'Escalated'])->required(), TextInput::make('classification'), TextInput::make('confidence')->numeric()->minValue(0)->maxValue(1), Textarea::make('response_draft')]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('subject')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('classification'), TextColumn::make('confidence'), TextColumn::make('escalation_level')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListAgentCases::route('/')];
    }
}
