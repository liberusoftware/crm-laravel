<?php

declare(strict_types=1);

namespace Tests\Feature\ChatAndBots;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ChatAndBots\Actions\CreateChatBot;
use Liberu\CRM\ChatAndBots\Actions\HandoffChatSession;
use Liberu\CRM\ChatAndBots\Actions\StartChatSession;
use Tests\TestCase;

final class ChatAndBotsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_bot_session_transcript_channels_and_handoff_are_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $bot = app(CreateChatBot::class)->execute($t->id, $u->id, 'Concierge', ['steps' => ['qualify', 'book']], ['web', 'whatsapp']);
        $bot->update(['status' => 'active']);
        $session = app(StartChatSession::class)->execute($t->id, $bot, 'visitor-1', 'web');
        $session = app(HandoffChatSession::class)->execute($t->id, $session, 'agent-1');
        $this->assertSame('handoff', $session->status);
        $this->assertSame('agent-1', $session->handoff_to);
    }
}
