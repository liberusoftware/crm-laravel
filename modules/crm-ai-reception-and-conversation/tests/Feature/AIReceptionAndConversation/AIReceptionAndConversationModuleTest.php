<?php

declare(strict_types=1);

namespace Tests\Feature\AIReceptionAndConversation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Liberu\CRM\AIReceptionAndConversation\Actions\ActivateReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Actions\CreateReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Actions\RecordReceptionTurn;
use Liberu\CRM\AIReceptionAndConversation\Actions\StartReceptionConversation;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionConversation;
use Liberu\CRM\AIReceptionAndConversation\Services\ReceptionResponder;
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

    public function test_ai_connection_failure_returns_the_configured_fallback(): void
    {
        config(['services.ai_reception.endpoint' => 'https://ai.example.test/respond', 'services.ai_reception.fallback_message' => 'Please contact support.']);
        Http::fake(fn () => throw new ConnectionException('Unavailable'));
        $reply = app(ReceptionResponder::class)->respond(
            new ReceptionAgent(),
            new ReceptionConversation(),
            'Help',
        );
        $this->assertSame('Please contact support.', $reply);
    }

    public function test_voice_reception_records_replies_and_routes_support_to_a_person(): void
    {
        config(['services.twilio.auth_token' => null]);
        $agent = app(CreateReceptionAgent::class)->execute(901, 7, [
            'name' => 'Voice support', 'channel' => 'voice', 'knowledge' => ['faq'],
            'policy' => ['transfer_numbers' => ['support' => '+15551234567']],
        ]);
        $agent = app(ActivateReceptionAgent::class)->execute(901, $agent);
        $url = route('twilio.ai-reception', ['agentId' => $agent->id]);
        $this->post($url, ['CallSid' => 'CA-test'])->assertOk()->assertSee('speech dtmf', false);
        $conversation = ReceptionConversation::query()->where('external_key', 'CA-test')->firstOrFail();
        $this->assertSame('assistant', $conversation->transcript[0]['role']);
        $this->post($url, ['CallSid' => 'CA-test', 'Digits' => '2'])->assertOk()->assertSee('+15551234567', false);
        $this->assertSame('handoff', $conversation->refresh()->status);
        $this->assertSame('requested', $conversation->handoff_status);
    }
}
