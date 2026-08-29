<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Filament\Resources\ProspectResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Prospecting\Filament\Resources\ProspectResource;

final class ListProspects extends ListRecords
{
    protected static string $resource = ProspectResource::class;
}
