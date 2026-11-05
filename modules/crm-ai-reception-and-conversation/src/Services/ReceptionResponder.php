<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversation\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionConversation;

final class ReceptionResponder
{
    public function respond(ReceptionAgent $agent, ReceptionConversation $conversation, string $input): string
    {
        $endpoint = (string) config('services.ai_reception.endpoint', '');
        if ($endpoint !== '') {
            try {
                $response = Http::withToken((string) config('services.ai_reception.api_key', ''))->acceptJson()->timeout(15)->retry(2, 200, throw: false)->post($endpoint, [
                'agent' => ['name' => $agent->name, 'knowledge' => $agent->knowledge, 'tools' => $agent->tools, 'policy' => $agent->policy],
                'conversation' => ['id' => $conversation->id, 'transcript' => $conversation->transcript],
                'input' => $input,
            ]);
            } catch (ConnectionException $exception) {
                report($exception);

                return (string) config('services.ai_reception.fallback_message', 'Please hold while we connect you to our team.');
            }
            $reply = $response->json('reply');
            if ($response->successful() && is_string($reply) && trim($reply) !== '') {
                return trim($reply);
            }
        }

        return (string) config('services.ai_reception.fallback_message');
    }
}
