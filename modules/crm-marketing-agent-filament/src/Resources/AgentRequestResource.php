<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgentFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\MarketingAgent\Models\AgentRequest;
use Liberu\CRM\MarketingAgentFilament\Resources\Pages\CreateAgentRequest;
use Liberu\CRM\MarketingAgentFilament\Resources\Pages\EditAgentRequest;
use Liberu\CRM\MarketingAgentFilament\Resources\Pages\ListAgentRequests;

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

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListAgentRequests::route('/'), 'create' => CreateAgentRequest::route('/create'), 'edit' => EditAgentRequest::route('/{record}/edit')];
    }
}
