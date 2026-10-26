<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ProductWorkspace\Models\WorkspaceProduct;
use Liberu\CRM\ProductWorkspaceFilament\Resources\Pages\CreateWorkspaceProduct;
use Liberu\CRM\ProductWorkspaceFilament\Resources\Pages\EditWorkspaceProduct;
use Liberu\CRM\ProductWorkspaceFilament\Resources\Pages\ListWorkspaceProducts;

final class WorkspaceProductResource extends Resource
{
    protected static ?string $model = WorkspaceProduct::class;

    protected static ?string $navigationLabel = 'Product workspace';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sku')->required()->maxLength(120),
            TextInput::make('name')->required()->maxLength(255),
            Textarea::make('description')->rows(5)->columnSpanFull(),
            TextInput::make('price')->numeric()->minValue(0),
            Select::make('currency')->options(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'CAD' => 'CAD', 'AUD' => 'AUD'])->default('USD')->required(),
            Select::make('sync_status')->options(['local' => 'Local', 'synced' => 'Synced', 'pending' => 'Pending', 'failed' => 'Failed'])->default('local')->required(),
            KeyValue::make('price_book')->json()->label('Price book')->columnSpanFull(),
            Select::make('eligible')->options([1 => 'Eligible', 0 => 'Not eligible'])->default(1)->required(),
            KeyValue::make('metadata')->json()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sku')->searchable()->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('price')->money(fn (WorkspaceProduct $record): string => (string) $record->getAttribute('currency'))->sortable(),
            TextColumn::make('currency'),
            IconColumn::make('eligible')->boolean()->sortable(),
            TextColumn::make('sync_status')->badge(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('name');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListWorkspaceProducts::route('/'), 'create' => CreateWorkspaceProduct::route('/create'), 'edit' => EditWorkspaceProduct::route('/{record}/edit')];
    }
}
