<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource;

final class ListAffiliates extends ListRecords
{
    protected static string $resource = AffiliateResource::class;
}
