<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\ProductWorkspaceFilament\Resources\WorkspaceProductResource;

final class CreateWorkspaceProduct extends CreateRecord
{
    protected static string $resource = WorkspaceProductResource::class;
}
