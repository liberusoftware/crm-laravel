<?php

declare(strict_types=1);

namespace Liberu\CRM\LoyaltyFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LoyaltyFilament\Resources\LoyaltyProgramResource;

final class ListLoyaltyPrograms extends ListRecords
{
    protected static string $resource = LoyaltyProgramResource::class;
}
