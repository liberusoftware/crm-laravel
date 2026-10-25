<?php

namespace Tests\Fixtures\HostNavigation;

use Filament\Resources\Resource;

final class Search extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = null;
}
