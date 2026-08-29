<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Filament\Resources\ReputationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ReputationManagement\Filament\Resources\ReputationResource;

final class ListReviews extends ListRecords
{
    protected static string $resource = ReputationResource::class;
}
