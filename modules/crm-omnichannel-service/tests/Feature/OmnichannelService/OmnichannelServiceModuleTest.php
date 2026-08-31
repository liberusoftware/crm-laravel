<?php

declare(strict_types=1);

namespace Tests\Feature\OmnichannelService;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\OmnichannelService\Actions\AssignConversation;
use Liberu\CRM\OmnichannelService\Actions\RecordInteraction;
use Liberu\CRM\OmnichannelService\Actions\RecordWorkspaceEvent;
use Liberu\CRM\OmnichannelService\Actions\StartConversation;
use Tests\TestCase;

final class OmnichannelServiceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_multichannel_workspace_history_and_collision_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $conversation = app(StartConversation::class)->execute($team->id, $owner->id, ['external_key' => 'chat-1', 'channel' => 'chat', 'subject' => 'Help']);
        app(AssignConversation::class)->execute($team->id, $owner->id, $conversation, ['assigned_to' => $owner->id]);
        app(RecordInteraction::class)->execute($team->id, $owner->id, $conversation, ['direction' => 'inbound', 'body' => 'Hello', 'occurred_at' => now()]);
        app(RecordWorkspaceEvent::class)->execute($team->id, $owner->id, $conversation, ['kind' => 'collision_lock', 'status' => 'active', 'expires_at' => now()->addMinutes(5)]);
        $this->assertDatabaseHas('crm_omnichannel_interactions', ['team_id' => $team->id, 'conversation_id' => $conversation->id]);
        $this->assertDatabaseHas('crm_omnichannel_workspace_events', ['team_id' => $team->id, 'kind' => 'collision_lock']);
        $this->assertDatabaseMissing('crm_omnichannel_conversations', ['team_id' => $other->id, 'external_key' => 'chat-1']);
    }
}
