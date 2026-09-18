<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\SalesPipelines\Filament\Resources\StageResource\Pages\CreateStage;
use Liberu\CRM\SalesPipelines\Filament\Resources\StageResource\Pages\EditStage;
use Liberu\CRM\SalesPipelines\Filament\Resources\StageResource\Pages\ListStages;
use Liberu\CRM\SalesPipelines\Models\SalesPipeline;
use Liberu\CRM\SalesPipelines\Models\SalesStage;

final class StageResource extends Resource
{
    protected static ?string $model = SalesStage::class;

    protected static ?string $navigationLabel = 'Pipeline stages';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('pipeline_id')->label('Pipeline')->options(fn (): array => SalesPipeline::query()->where('team_id', PipelineResource::teamId())->where('active', true)->orderBy('name')->pluck('name', 'id')->all())->searchable()->required(),
            TextInput::make('name')->required()->maxLength(160),
            TextInput::make('position')->numeric()->minValue(1)->required(),
            TextInput::make('probability')->numeric()->minValue(0)->maxValue(100)->suffix('%')->required(),
            TextInput::make('rotting_days')->label('Rotting after days')->numeric()->minValue(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('pipeline.name')->label('Pipeline')->searchable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('position')->sortable(),
            TextColumn::make('probability')->suffix('%')->sortable(),
            TextColumn::make('rotting_days')->label('Rotting days'),
        ])->defaultSort('position');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('pipeline', fn (Builder $query): Builder => $query->where('team_id', PipelineResource::teamId()));
    }

    public static function getPages(): array
    {
        return ['index' => ListStages::route('/'), 'create' => CreateStage::route('/create'), 'edit' => EditStage::route('/{record}/edit')];
    }
}
