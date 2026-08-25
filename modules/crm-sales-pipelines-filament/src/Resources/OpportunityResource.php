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
use Liberu\CRM\SalesPipelines\Filament\Resources\OpportunityResource\Pages\ListOpportunities;
use Liberu\CRM\SalesPipelines\Models\Opportunity;

final class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('name')->required(), TextInput::make('value')->numeric(), TextInput::make('probability')->numeric(), TextInput::make('close_date'), Select::make('status')->options(['open' => 'Open', 'won' => 'Won', 'lost' => 'Lost'])]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('value'), TextColumn::make('probability'), TextColumn::make('close_date')->date()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListOpportunities::route('/')];
    }
}
