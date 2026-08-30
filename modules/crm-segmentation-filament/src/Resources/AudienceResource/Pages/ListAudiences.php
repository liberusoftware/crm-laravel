<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Filament\Resources\AudienceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Segmentation\Filament\Resources\AudienceResource;

final class ListAudiences extends ListRecords
{
    protected static string $resource = AudienceResource::class;
}
