<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources\StageResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\SalesPipelines\Filament\Resources\StageResource;

final class ListStages extends ListRecords
{
    protected static string $resource = StageResource::class;
}
