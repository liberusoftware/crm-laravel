<?php

declare(strict_types=1);

namespace Tests\Feature\UsageWallet;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\UnifiedConversations\Actions\OpenConversation;
use Liberu\CRM\UnifiedConversations\Actions\SendMessage;
use Liberu\CRM\UnifiedConversations\Models\Conversation;
use Liberu\CRM\UnifiedConversations\Models\ConversationMessage;
use Tests\TestCase;

final class UnifiedConversationsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversations_and_messages_are_team_scoped_and_idempotent(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $conversation = app(OpenConversation::class)->execute($team->id, $owner->id, ['channel' => 'email', 'external_id' => 'x', 'subject' => 'Hello']);
        $same = app(OpenConversation::class)->execute($team->id, $owner->id, ['channel' => 'email', 'external_id' => 'x']);
        $message = app(SendMessage::class)->execute($team->id, $owner->id, $conversation->id, ['body' => 'Hi', 'idempotency_key' => 'm1']);
        self::assertSame($conversation->id, $same->id);
        self::assertSame($message->id, app(SendMessage::class)->execute($team->id, $owner->id, $conversation->id, ['body' => 'changed', 'idempotency_key' => 'm1'])->id);
        self::assertSame(1, Conversation::query()->where('team_id', $team->id)->count());
        self::assertSame(1, ConversationMessage::query()->where('team_id', $team->id)->count());
        self::assertSame(0, Conversation::query()->where('team_id', $other->id)->count());
    }
}
