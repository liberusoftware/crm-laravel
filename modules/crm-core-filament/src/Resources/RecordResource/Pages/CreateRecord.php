<?php

namespace Liberu\CRM\Core\Filament\Resources\RecordResource\Pages;

use Filament\Resources\Pages\CreateRecord as BaseCreateRecord;
use Liberu\CRM\Core\Filament\Resources\RecordResource;

final class CreateRecord extends BaseCreateRecord
{
    protected static string $resource = RecordResource::class;
}
