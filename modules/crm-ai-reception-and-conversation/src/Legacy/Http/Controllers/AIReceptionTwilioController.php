<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\CRM\AIReceptionAndConversation\Actions\RecordReceptionTurn;
use Liberu\CRM\AIReceptionAndConversation\Actions\RequestReceptionHandoff;
use Liberu\CRM\AIReceptionAndConversation\Actions\StartReceptionConversation;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionConversation;
use Liberu\CRM\AIReceptionAndConversation\Services\ReceptionResponder;
use Twilio\TwiML\VoiceResponse;

final class AIReceptionTwilioController extends Controller
{
    public function handle(Request $request, int $agentId, StartReceptionConversation $start, RecordReceptionTurn $record, ReceptionResponder $responder): Response
    {
        $request->validate(['CallSid' => ['required', 'string', 'max:64'], 'SpeechResult' => ['nullable', 'string', 'max:10000'], 'Digits' => ['nullable', 'string', 'max:1']]);
        $agent = ReceptionAgent::query()->whereKey($agentId)->where('status', 'active')->where('channel', 'voice')->firstOrFail();
        $callSid = trim((string) $request->input('CallSid'));
        abort_unless($callSid !== '', 422);

        $conversation = ReceptionConversation::query()->firstWhere([
            'team_id' => $agent->team_id,
            'agent_id' => $agent->id,
            'external_key' => $callSid,
        ]);
        if ($conversation === null) {
            $conversation = $start->execute((int) $agent->team_id, $agent, ['external_key' => $callSid]);
        }

        $speech = trim((string) $request->input('SpeechResult'));
        if ($speech !== '' && $conversation->status === 'active') {
            $record->execute((int) $agent->team_id, 0, $conversation, ['role' => 'user', 'content' => $speech]);
        }

        $response = new VoiceResponse();
        $department = match (strtolower(trim((string) $request->input('Digits')))) {
            '1' => 'sales',
            '2' => 'support',
            '0' => 'human',
            default => preg_match('/\b(agent|human|operator)\b/i', $speech) ? 'human' : null,
        };
        $destination = $department === null ? null : ($agent->policy['transfer_numbers'][$department] ?? null);
        if (is_string($destination) && preg_match('/^\+[1-9][0-9]{7,14}$/', $destination)) {
            if ($conversation->status === 'active') {
                app(RequestReceptionHandoff::class)->execute((int) $agent->team_id, 0, $conversation, 'Requested '.$department);
            }
            $response->dial($destination);
            $response->hangup();

            return response((string) $response)->header('Content-Type', 'text/xml');
        }
        if ($conversation->status !== 'active') {
            $response->say('Your conversation has been referred to our team. Please call again for further assistance.');
            $response->hangup();

            return response((string) $response)->header('Content-Type', 'text/xml');
        }
        $reply = $speech === ''
            ? (string) config('services.ai_reception.greeting', 'Hello. How can I help? Press 1 for sales, 2 for support, or 0 for a person.')
            : $responder->respond($agent, $conversation->refresh(), $speech);
        $record->execute((int) $agent->team_id, 0, $conversation, ['role' => 'assistant', 'content' => $reply]);
        $gather = $response->gather([
            'input' => 'speech dtmf',
            'numDigits' => 1,
            'action' => route('twilio.ai-reception', ['agentId' => $agent->id]),
            'method' => 'POST',
            'speechTimeout' => 'auto',
            'language' => config('services.ai_reception.language', 'en-GB'),
        ]);
        $gather->say($reply);
        $response->say((string) config('services.ai_reception.no_input_message', 'I did not hear anything. Please call again when you are ready.'));

        return response((string) $response)->header('Content-Type', 'text/xml');
    }
}
