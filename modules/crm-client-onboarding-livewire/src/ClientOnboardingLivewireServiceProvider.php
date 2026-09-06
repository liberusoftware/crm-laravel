<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboardingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ClientOnboardingLivewire\Components\OnboardingBrowser;
use Livewire\Livewire;

final class ClientOnboardingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-client-onboarding::onboarding-browser', OnboardingBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-client-onboarding');
    }
}
