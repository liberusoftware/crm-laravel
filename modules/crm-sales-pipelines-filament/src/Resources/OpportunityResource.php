<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource\Pages\CreateOpportunity;
use Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource\Pages\EditOpportunity;
use Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource\Pages\ListOpportunities;
use Liberu\CRM\SalesPipelines\Models\Opportunity;
use Liberu\CRM\SalesPipelines\Models\SalesPipeline;
use Liberu\CRM\SalesPipelines\Models\SalesStage;

final class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([
            Select::make('pipeline_id')
                ->options(fn (): array => SalesPipeline::query()->where('team_id', self::teamId())->where('active', true)->pluck('name', 'id')->all())
                ->required()
                ->live(),
            Select::make('stage_id')
                ->options(fn (Get $get): array => SalesStage::query()->where('pipeline_id', $get('pipeline_id'))->pluck('name', 'id')->all())
                ->required(),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('value')->numeric()->minValue(0),
            TextInput::make('probability')->numeric()->minValue(0)->maxValue(100),
            DatePicker::make('close_date'),
            Select::make('status')->options(['open' => 'Open', 'won' => 'Won', 'lost' => 'Lost'])->required(),
        ]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('value'), TextColumn::make('probability'), TextColumn::make('close_date')->date()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', self::teamId());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOpportunities::route('/'),
            'create' => CreateOpportunity::route('/create'),
            'edit' => EditOpportunity::route('/{record}/edit'),
        ];
    }

    private static function teamId(): int
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return (int) $id;
    }
}
