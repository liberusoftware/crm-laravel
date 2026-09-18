<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources\StageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\SalesPipelines\Filament\Resources\StageResource;

final class CreateStage extends CreateRecord
{
    protected static string $resource = StageResource::class;
}
