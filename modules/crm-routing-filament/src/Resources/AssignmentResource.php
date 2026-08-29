<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Routing\Filament\Resources\AssignmentResource\Pages\CreateAssignment;
use Liberu\CRM\Routing\Filament\Resources\AssignmentResource\Pages\ListAssignments;
use Liberu\CRM\Routing\Models\RoutingAssignment;

final class AssignmentResource extends Resource
{
    protected static ?string $model = RoutingAssignment::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([
            Select::make('subject_type')->options(['lead' => 'Lead', 'contact' => 'Contact', 'opportunity' => 'Opportunity'])->required(),
            Select::make('subject_id')->required()->native(false),
        ]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('subject_type'), TextColumn::make('subject_id'), TextColumn::make('agent_id'), TextColumn::make('status')->badge(), TextColumn::make('acceptance_due_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListAssignments::route('/'), 'create' => CreateAssignment::route('/create')];
    }
}
