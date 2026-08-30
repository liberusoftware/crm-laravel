<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\UnifiedConversations\Actions\SyncExternalConversation;

final class ZernioWebhookController extends Controller
{
    public function __invoke(Request $request, Team $team, SyncExternalConversation $sync): JsonResponse
    {
        $secret = (string) config('services.zernio.webhook_secret');
        $signature = (string) ($request->header('X-Zernio-Signature-256') ?? $request->header('X-Zernio-Signature'));
        $signature = str_starts_with($signature, 'sha256=') ? substr($signature, 7) : $signature;
        $payload = (string) $request->getContent();
        abort_unless($secret !== '' && $signature !== '' && hash_equals(hash_hmac('sha256', $payload, $secret), $signature), 401);

        $data = $request->json()->all();
        $profileId = (string) data_get($data, 'profileId', data_get($data, 'profile.id', ''));
        abort_unless($profileId !== '' && hash_equals($profileId, (string) $team->zernio_profile_id), 403);
        $conversation = data_get($data, 'conversation', $data);
        abort_unless(is_array($conversation), 422);

        $sync->execute((int) $team->id, [
            'channel' => 'zernio.'.((string) ($conversation['platform'] ?? 'social')),
            'external_id' => (string) ($conversation['id'] ?? ''),
            'subject' => $conversation['participantName'] ?? 'Social conversation',
            'status' => $conversation['status'] ?? 'open',
            'participant' => ['identity' => $conversation['participantId'] ?? $conversation['id'], 'name' => $conversation['participantName'] ?? null],
            'message' => ['external_id' => $conversation['messageId'] ?? $conversation['lastMessageId'] ?? $conversation['id'], 'body' => $conversation['message'] ?? $conversation['lastMessage'] ?? '', 'direction' => 'inbound'],
            'metadata' => ['zernio_event' => $data],
        ]);

        return response()->json(['received' => true]);
    }
}
