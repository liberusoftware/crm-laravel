<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources\PipelineResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\SalesPipelines\Filament\Resources\PipelineResource;

final class CreatePipeline extends CreateRecord
{
    protected static string $resource = PipelineResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => PipelineResource::teamId()];
    }
}
