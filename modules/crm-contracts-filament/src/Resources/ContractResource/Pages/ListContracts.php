<?php

declare(strict_types=1);

namespace Liberu\CRM\ContractsFilament\Resources\ContractResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ContractsFilament\Resources\ContractResource;

final class ListContracts extends ListRecords
{
    protected static string $resource = ContractResource::class;
}
