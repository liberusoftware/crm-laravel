<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources\PipelineResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\SalesPipelines\Filament\Resources\PipelineResource;

final class ListPipelines extends ListRecords
{
    protected static string $resource = PipelineResource::class;
}
