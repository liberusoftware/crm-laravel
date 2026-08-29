<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource\Pages\CreatePlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource\Pages\EditPlaybook;
use Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource\Pages\ListPlaybooks;
use Liberu\CRM\PlaybooksAndEnablement\Models\Playbook;

final class PlaybookResource extends Resource
{
    protected static ?string $model = Playbook::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), Select::make('kind')->options(['script' => 'Guided script', 'qualification' => 'Qualification card', 'battlecard' => 'Battlecard', 'onboarding' => 'Onboarding', 'coaching' => 'Coaching'])->required(), Textarea::make('description'), Textarea::make('steps')->json()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('kind')->badge(), TextColumn::make('active')->badge(), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListPlaybooks::route('/'), 'create' => CreatePlaybook::route('/create'), 'edit' => EditPlaybook::route('/{record}/edit')];
    }
}
