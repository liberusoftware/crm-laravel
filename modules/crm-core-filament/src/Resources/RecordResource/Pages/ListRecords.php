<?php

namespace Liberu\CRM\Core\Filament\Resources\RecordResource\Pages;

use Filament\Resources\Pages\ListRecords as BaseListRecords;
use Liberu\CRM\Core\Filament\Resources\RecordResource;

final class ListRecords extends BaseListRecords
{
    protected static string $resource = RecordResource::class;
}
