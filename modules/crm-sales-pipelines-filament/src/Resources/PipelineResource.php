<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\SalesPipelines\Filament\Resources\PipelineResource\Pages\CreatePipeline;
use Liberu\CRM\SalesPipelines\Filament\Resources\PipelineResource\Pages\EditPipeline;
use Liberu\CRM\SalesPipelines\Filament\Resources\PipelineResource\Pages\ListPipelines;
use Liberu\CRM\SalesPipelines\Models\SalesPipeline;

final class PipelineResource extends Resource
{
    protected static ?string $model = SalesPipeline::class;

    protected static ?string $navigationLabel = 'Sales pipelines';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(160),
            Toggle::make('active')->label('Available for new opportunities')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('stages_count')->counts('stages')->label('Stages'),
            TextColumn::make('active')->formatStateUsing(static fn (mixed $state): string => (bool) $state ? 'Active' : 'Archived'),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', self::teamId());
    }

    public static function getPages(): array
    {
        return ['index' => ListPipelines::route('/'), 'create' => CreatePipeline::route('/create'), 'edit' => EditPipeline::route('/{record}/edit')];
    }

    public static function teamId(): int
    {
        $id = auth()->user()?->getAttribute('current_team_id');
        abort_unless($id !== null, 403);

        return (int) $id;
    }
}
