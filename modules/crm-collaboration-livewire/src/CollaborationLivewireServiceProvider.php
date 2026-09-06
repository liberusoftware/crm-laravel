<?php

declare(strict_types=1);

namespace Liberu\CRM\CollaborationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CollaborationLivewire\Components\CollaborationBrowser;
use Livewire\Livewire;

final class CollaborationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-collaboration::collaboration-browser', CollaborationBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-collaboration');
    }
}
