<?php

declare(strict_types=1);

namespace Liberu\CRM\LoyaltyFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\LoyaltyFilament\Resources\LoyaltyProgramResource;

final class EditLoyaltyProgram extends EditRecord
{
    protected static string $resource = LoyaltyProgramResource::class;
}
