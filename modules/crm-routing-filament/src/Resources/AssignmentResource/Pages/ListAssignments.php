<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Filament\Resources\AssignmentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Routing\Filament\Resources\AssignmentResource;

final class ListAssignments extends ListRecords
{
    protected static string $resource = AssignmentResource::class;
}
