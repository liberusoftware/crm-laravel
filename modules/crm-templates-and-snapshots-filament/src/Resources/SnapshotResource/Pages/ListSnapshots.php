<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource;

final class ListSnapshots extends ListRecords
{
    protected static string $resource = SnapshotResource::class;
}
