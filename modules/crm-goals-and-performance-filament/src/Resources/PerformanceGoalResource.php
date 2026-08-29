<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformanceFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\GoalsAndPerformance\Models\PerformanceGoal;

final class PerformanceGoalResource extends Resource
{
    protected static ?string $model = PerformanceGoal::class;

    protected static ?string $navigationLabel = 'Performance';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
