<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\ProductWorkspaceFilament\Resources\WorkspaceProductResource;

final class EditWorkspaceProduct extends EditRecord
{
    protected static string $resource = WorkspaceProductResource::class;
}
