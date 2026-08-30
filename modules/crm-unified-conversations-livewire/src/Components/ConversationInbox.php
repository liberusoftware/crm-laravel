<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\UnifiedConversations\Queries\ConversationQuery;
use Livewire\Component;

final class ConversationInbox extends Component
{
    public function render(ConversationQuery $q): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-unified-conversations-livewire::inbox', ['conversations' => $q->list((int) $id)]);
    }
}
