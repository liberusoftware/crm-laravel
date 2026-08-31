<?php

declare(strict_types=1);

namespace Tests\Feature\AIReceptionAndConversation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\AIReceptionAndConversation\Actions\ActivateReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Actions\CreateReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Actions\RecordReceptionTurn;
use Liberu\CRM\AIReceptionAndConversation\Actions\StartReceptionConversation;
use Tests\TestCase;

final class AIReceptionAndConversationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_governed_agent_records_confidence_qualification_booking_and_audit(): void
    {
        $agent = app(CreateReceptionAgent::class)->execute(901, 7, ['name' => 'Receptionist', 'channel' => 'chat', 'knowledge' => ['faq'], 'tools' => ['calendar']]);
        $agent = app(ActivateReceptionAgent::class)->execute(901, $agent);
        $conversation = app(StartReceptionConversation::class)->execute(901, $agent, ['external_key' => 'visitor-1']);
        $conversation = app(RecordReceptionTurn::class)->execute(901, 7, $conversation, ['role' => 'assistant', 'content' => 'How can I help?', 'confidence' => .94, 'qualification' => ['qualified' => true], 'booking' => ['requested' => true], 'summary' => 'Qualified visitor']);

        $this->assertSame('active', $agent->status);
        $this->assertSame('0.9400', (string) $conversation->confidence);
        $this->assertTrue($conversation->qualification['qualified']);
        $this->assertCount(1, $conversation->audits()->get());
    }
}
