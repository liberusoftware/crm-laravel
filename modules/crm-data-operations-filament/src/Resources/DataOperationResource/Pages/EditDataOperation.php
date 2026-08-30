<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource;

final class EditDataOperation extends EditRecord
{
    protected static string $resource = DataOperationResource::class;
}
