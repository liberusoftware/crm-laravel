<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ProductWorkspaceFilament\Resources\WorkspaceProductResource;

final class ListWorkspaceProducts extends ListRecords
{
    protected static string $resource = WorkspaceProductResource::class;
}
