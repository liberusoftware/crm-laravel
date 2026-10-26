<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource;

final class ListAgencyAccounts extends ListRecords
{
    protected static string $resource = AgencyAccountResource::class;
}
