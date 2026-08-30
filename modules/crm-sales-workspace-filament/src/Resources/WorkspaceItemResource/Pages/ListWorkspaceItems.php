<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Filament\Resources\WorkspaceItemResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\SalesWorkspace\Filament\Resources\WorkspaceItemResource;

final class ListWorkspaceItems extends ListRecords
{
    protected static string $resource = WorkspaceItemResource::class;
}
