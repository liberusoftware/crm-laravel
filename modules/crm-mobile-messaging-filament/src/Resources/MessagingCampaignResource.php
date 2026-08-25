<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessagingFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\MobileMessaging\Models\MessagingCampaign;

final class MessagingCampaignResource extends Resource
{
    protected static ?string $model = MessagingCampaign::class;

    protected static ?string $navigationLabel = 'Mobile Messaging';

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
