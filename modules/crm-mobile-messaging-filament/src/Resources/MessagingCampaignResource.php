<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessagingFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\MobileMessaging\Models\MessagingCampaign;
use Liberu\CRM\MobileMessagingFilament\Resources\Pages\CreateMessagingCampaign;
use Liberu\CRM\MobileMessagingFilament\Resources\Pages\EditMessagingCampaign;
use Liberu\CRM\MobileMessagingFilament\Resources\Pages\ListMessagingCampaigns;

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

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListMessagingCampaigns::route('/'), 'create' => CreateMessagingCampaign::route('/create'), 'edit' => EditMessagingCampaign::route('/{record}/edit')];
    }
}
