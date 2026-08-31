<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource;

final class ListAccountPlanningRecords extends ListRecords
{
    protected static string $resource = AccountPlanningRecordResource::class;
}
