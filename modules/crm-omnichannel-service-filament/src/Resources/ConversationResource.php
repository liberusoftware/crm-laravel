<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelServiceFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\OmnichannelService\Models\Conversation;

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

    public static function getPages(): array
    {
        return [];
    }
}
