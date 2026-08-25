<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResourcesFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\MarketingResources\Models\MarketingResource;

final class MarketingResourceResource extends Resource
{
    protected static ?string $model = MarketingResource::class;

    protected static ?string $navigationLabel = 'Marketing Resources';

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
