<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomerLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\FeedbackAndVoiceOfCustomerLivewire\Livewire\FeedbackDashboard;
use Livewire\Livewire;

final class FeedbackAndVoiceOfCustomerLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-feedback-and-voice-of-customer-livewire');
        Livewire::component('module-crm-feedback-and-voice-of-customer-livewire::dashboard', FeedbackDashboard::class);
    }
}
