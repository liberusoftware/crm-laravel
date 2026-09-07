<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament\Resources\StageResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\SalesPipelines\Filament\Resources\StageResource;

final class EditStage extends EditRecord
{
    protected static string $resource = StageResource::class;
}
