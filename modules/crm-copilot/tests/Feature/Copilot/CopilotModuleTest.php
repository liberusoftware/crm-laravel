<?php

declare(strict_types=1);

namespace Tests\Feature\Copilot;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Copilot\Actions\AskCopilot;
use Liberu\CRM\Copilot\Actions\ConfirmCopilotAction;
use Liberu\CRM\Copilot\Actions\ProposeCopilotAction;
use Tests\TestCase;

final class CopilotModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_bounded_request_requires_explicit_action_confirmation(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $request = app(AskCopilot::class)->execute($team->id, $user->id, ['kind' => 'summary', 'input' => 'Summarize this account', 'context' => ['record_key' => 'account-1']]);
        $action = app(ProposeCopilotAction::class)->execute($team->id, $user->id, $request, ['action' => 'create_task', 'payload' => ['title' => 'Follow up']]);
        $confirmed = app(ConfirmCopilotAction::class)->execute($team->id, $user->id, $action);
        $this->assertSame('completed', $request->status);
        $this->assertSame('confirmed', $confirmed->status);
        $this->assertNotNull($confirmed->confirmed_at);
    }
}
