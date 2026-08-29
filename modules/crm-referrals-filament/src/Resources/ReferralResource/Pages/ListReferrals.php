<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Filament\Resources\ReferralResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Referrals\Filament\Resources\ReferralResource;

final class ListReferrals extends ListRecords
{
    protected static string $resource = ReferralResource::class;
}
