<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Filament\Resources\ProjectResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Projects\Filament\Resources\ProjectResource;

final class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;
}
