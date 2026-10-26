<?php

declare(strict_types=1);

namespace Liberu\CRM\CommunitiesFilament\Resources\CommunitySpaceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CommunitiesFilament\Resources\CommunitySpaceResource;

final class ListCommunitySpaces extends ListRecords
{
    protected static string $resource = CommunitySpaceResource::class;
}
