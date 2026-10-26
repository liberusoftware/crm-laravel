<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingFilament\Resources\AccountBasedMarketingRecordResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AccountBasedMarketingFilament\Resources\AccountBasedMarketingRecordResource;

final class ListAccountBasedMarketingRecords extends ListRecords
{
    protected static string $resource = AccountBasedMarketingRecordResource::class;
}
