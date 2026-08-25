<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\RevenueIntelligence\Filament\Resources\InsightResource\Pages\ListInsights;
use Liberu\CRM\RevenueIntelligence\Models\RevenueInsight;

final class InsightResource extends Resource
{
    protected static ?string $model = RevenueInsight::class;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind'), TextColumn::make('subject_type'), TextColumn::make('subject_id'), TextColumn::make('score'), TextColumn::make('severity')->badge(), TextColumn::make('observed_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListInsights::route('/')];
    }
}
