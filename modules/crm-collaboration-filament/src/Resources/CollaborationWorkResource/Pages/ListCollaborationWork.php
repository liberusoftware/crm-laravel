<?php

declare(strict_types=1);

namespace Liberu\CRM\CollaborationFilament\Resources\CollaborationWorkResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CollaborationFilament\Resources\CollaborationWorkResource;

final class ListCollaborationWork extends ListRecords
{
    protected static string $resource = CollaborationWorkResource::class;
}
