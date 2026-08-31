<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResourcesFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\MarketingResources\Models\MarketingResource;
use Liberu\CRM\MarketingResourcesFilament\Resources\Pages\CreateMarketingResource;
use Liberu\CRM\MarketingResourcesFilament\Resources\Pages\EditMarketingResource;
use Liberu\CRM\MarketingResourcesFilament\Resources\Pages\ListMarketingResources;

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

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListMarketingResources::route('/'), 'create' => CreateMarketingResource::route('/create'), 'edit' => EditMarketingResource::route('/{record}/edit')];
    }
}
