<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\LeadQualification\Filament\Resources\QualificationFrameworkResource\Pages\CreateQualificationFramework;
use Liberu\CRM\LeadQualification\Filament\Resources\QualificationFrameworkResource\Pages\ListQualificationFrameworks;
use Liberu\CRM\LeadQualification\Models\QualificationFramework;

final class QualificationFrameworkResource extends Resource
{
    protected static ?string $model = QualificationFramework::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required()->maxLength(160), Select::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'])->required(), TextInput::make('mql_threshold')->numeric()->minValue(0)->maxValue(100)->required(), TextInput::make('pql_threshold')->numeric()->minValue(0)->maxValue(100)->required(), TextInput::make('sql_threshold')->numeric()->minValue(0)->maxValue(100)->required(), TextInput::make('service_qualified_threshold')->numeric()->minValue(0)->maxValue(100)->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('mql_threshold'), TextColumn::make('pql_threshold'), TextColumn::make('sql_threshold'), TextColumn::make('service_qualified_threshold')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListQualificationFrameworks::route('/'), 'create' => CreateQualificationFramework::route('/create')];
    }
}
