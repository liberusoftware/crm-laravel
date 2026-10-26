<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ChatAndBotsLivewire\Components\BotBrowser;
use Livewire\Livewire;

final class ChatAndBotsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-chat-and-bots::bot-browser', BotBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-chat-and-bots');
    }
}
