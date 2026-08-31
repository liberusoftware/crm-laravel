<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\CRM\AIReceptionAndConversation\Actions\RecordReceptionTurn;
use Liberu\CRM\AIReceptionAndConversation\Actions\StartReceptionConversation;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionConversation;
use Liberu\CRM\AIReceptionAndConversation\Services\ReceptionResponder;
use Twilio\TwiML\VoiceResponse;

final class AIReceptionTwilioController extends Controller
{
    public function handle(Request $request, int $agentId, StartReceptionConversation $start, RecordReceptionTurn $record, ReceptionResponder $responder): Response
    {
        $agent = ReceptionAgent::query()->whereKey($agentId)->where('status', 'active')->firstOrFail();
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
        if ($speech !== '') {
            $record->execute((int) $agent->team_id, 0, $conversation, ['role' => 'user', 'content' => $speech]);
        }

        $response = new VoiceResponse();
        $gather = $response->gather([
            'input' => 'speech',
            'action' => route('twilio.ai-reception', ['agentId' => $agent->id]),
            'method' => 'POST',
            'speechTimeout' => 'auto',
            'language' => config('services.ai_reception.language', 'en-GB'),
        ]);
        $gather->say($speech === ''
            ? (string) config('services.ai_reception.greeting', 'Hello, you have reached our team. How can I help you today?')
            : $responder->respond($agent, $conversation, $speech));
        $response->say((string) config('services.ai_reception.no_input_message', 'I did not hear anything. Please call again when you are ready.'));

        return response((string) $response)->header('Content-Type', 'text/xml');
    }
}
