<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgentFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\MarketingAgent\Models\AgentRequest;

final class AgentRequestResource extends Resource
{
    protected static ?string $model = AgentRequest::class;

    protected static ?string $navigationLabel = 'Marketing Agent';

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
