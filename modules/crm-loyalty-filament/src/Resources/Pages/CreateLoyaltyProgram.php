<?php

declare(strict_types=1);

namespace Liberu\CRM\LoyaltyFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\LoyaltyFilament\Resources\LoyaltyProgramResource;

final class CreateLoyaltyProgram extends CreateRecord
{
    protected static string $resource = LoyaltyProgramResource::class;
}
