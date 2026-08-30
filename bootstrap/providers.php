<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\Filament\PortalPanelProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\JetstreamServiceProvider;
use App\Providers\RouteServiceProvider;
use App\Providers\SocialstreamServiceProvider;
use App\Providers\TeamServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    EventServiceProvider::class,
    AdminPanelProvider::class,
    AppPanelProvider::class,
    PortalPanelProvider::class,
    FortifyServiceProvider::class,
    HorizonServiceProvider::class,
    JetstreamServiceProvider::class,
    RouteServiceProvider::class,
    SocialstreamServiceProvider::class,
    TeamServiceProvider::class,
];
