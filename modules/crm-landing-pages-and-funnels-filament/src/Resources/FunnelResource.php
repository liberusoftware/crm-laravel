<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnelsFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\LandingPagesAndFunnels\Models\Funnel;

final class FunnelResource extends Resource
{
    protected static ?string $model = Funnel::class;

    protected static ?string $navigationLabel = 'Funnels';

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
