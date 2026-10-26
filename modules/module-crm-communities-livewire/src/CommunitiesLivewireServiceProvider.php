<?php

declare(strict_types=1);

namespace Liberu\CRM\CommunitiesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CommunitiesLivewire\Components\CommunityBrowser;
use Livewire\Livewire;

final class CommunitiesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-communities::community-browser', CommunityBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-communities');
    }
}
