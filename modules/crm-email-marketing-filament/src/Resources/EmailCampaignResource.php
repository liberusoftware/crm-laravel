<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\EmailMarketing\Models\EmailCampaign;

final class EmailCampaignResource extends Resource
{
    protected static ?string $model = EmailCampaign::class;

    protected static ?string $navigationLabel = 'Email campaigns';

    public static function getPages(): array
    {
        return [];
    }
}
