<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\UnifiedConversations\Livewire\Components\ConversationInbox;
use Livewire\Livewire;

final class UnifiedConversationsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-unified-conversations::inbox', ConversationInbox::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-unified-conversations-livewire');
    }
}
