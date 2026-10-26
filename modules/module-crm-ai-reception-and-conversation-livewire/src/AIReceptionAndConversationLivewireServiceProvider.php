<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AIReceptionAndConversationLivewire\Components\AgentBrowser;
use Livewire\Livewire;

final class AIReceptionAndConversationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-ai-reception-and-conversation::agent-browser', AgentBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-ai-reception-and-conversation');
    }
}
