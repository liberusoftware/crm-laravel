<?php

namespace Tests\Fixtures\HostNavigation;

use Filament\Resources\Resource;

final class Ticket extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = null;
}
