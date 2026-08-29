<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource;

final class ListSequences extends ListRecords
{
    protected static string $resource = SequenceResource::class;
}
