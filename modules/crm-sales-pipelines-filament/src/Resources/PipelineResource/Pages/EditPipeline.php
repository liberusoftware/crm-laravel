<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources\PipelineResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\SalesPipelines\Filament\Resources\PipelineResource;

final class EditPipeline extends EditRecord
{
    protected static string $resource = PipelineResource::class;
}
