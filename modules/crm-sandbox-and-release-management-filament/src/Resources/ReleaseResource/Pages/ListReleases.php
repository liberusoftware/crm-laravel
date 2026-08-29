<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Filament\Resources\ReleaseResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\SandboxAndReleaseManagement\Filament\Resources\ReleaseResource;

final class ListReleases extends ListRecords
{
    protected static string $resource = ReleaseResource::class;
}
