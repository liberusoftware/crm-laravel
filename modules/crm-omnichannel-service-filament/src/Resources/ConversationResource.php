<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelServiceFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\OmnichannelService\Models\Conversation;
use Liberu\CRM\OmnichannelServiceFilament\Resources\Pages\CreateConversation;
use Liberu\CRM\OmnichannelServiceFilament\Resources\Pages\EditConversation;
use Liberu\CRM\OmnichannelServiceFilament\Resources\Pages\ListConversations;

final class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $navigationLabel = 'Omnichannel';

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
        return ['index' => ListConversations::route('/'), 'create' => CreateConversation::route('/create'), 'edit' => EditConversation::route('/{record}/edit')];
    }
}
