<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\EmailMarketing\Models\EmailCampaign;
use Liberu\CRM\EmailMarketingFilament\Resources\Pages\CreateEmailCampaign;
use Liberu\CRM\EmailMarketingFilament\Resources\Pages\EditEmailCampaign;
use Liberu\CRM\EmailMarketingFilament\Resources\Pages\ListEmailCampaigns;

final class EmailCampaignResource extends Resource
{
    protected static ?string $model = EmailCampaign::class;

    protected static ?string $navigationLabel = 'Email campaigns';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListEmailCampaigns::route('/'), 'create' => CreateEmailCampaign::route('/create'), 'edit' => EditEmailCampaign::route('/{record}/edit')];
    }
}
