<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestrationFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\JourneyOrchestration\Models\Journey;

final class JourneyResource extends Resource
{
    protected static ?string $model = Journey::class;

    protected static ?string $navigationLabel = 'Journeys';

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
