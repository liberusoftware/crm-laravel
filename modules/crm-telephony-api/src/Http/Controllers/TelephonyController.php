<?php

declare(strict_types=1);

namespace Liberu\CRM\TelephonyApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Telephony\Actions\ConfigureTelephony;
use Liberu\CRM\Telephony\Actions\CreateTelephonyQueue;
use Liberu\CRM\Telephony\Actions\LogTelephonyCall;
use Liberu\CRM\Telephony\Actions\UpdateCall;
use Liberu\CRM\Telephony\Actions\UpsertTelephonyNumber;
use Liberu\CRM\Telephony\Queries\TelephonyQuery;

final class TelephonyController extends Controller
{
    private function team(Request $request): int
    {
        abort_unless($request->user()?->current_team_id !== null, 403);

        return (int) $request->user()->current_team_id;
    }

    public function numbers(Request $r, TelephonyQuery $q): JsonResponse
    {
        return response()->json(['data' => $q->numbers($this->team($r))->get()]);
    }

    public function queues(Request $r, TelephonyQuery $q): JsonResponse
    {
        return response()->json(['data' => $q->queues($this->team($r))->get()]);
    }

    public function settings(Request $r, TelephonyQuery $q): JsonResponse
    {
        return response()->json(['data' => $q->settings($this->team($r))]);
    }

    public function configure(Request $r, ConfigureTelephony $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $r->validate(['provider' => ['required', 'string', 'max:50'], 'business_hours' => ['nullable', 'array'], 'ivr' => ['nullable', 'array'], 'skills' => ['nullable', 'array']]))]);
    }

    public function number(Request $r, UpsertTelephonyNumber $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $r->validate(['number' => ['required', 'string', 'max:32'], 'label' => ['nullable', 'string', 'max:255'], 'provider' => ['required', 'string', 'max:50'], 'status' => ['sometimes', 'in:active,inactive'], 'caller_id_enabled' => ['sometimes', 'boolean'], 'metadata' => ['nullable', 'array']]))], 201);
    }

    public function queue(Request $r, CreateTelephonyQueue $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $r->validate(['name' => ['required', 'string', 'max:255'], 'strategy' => ['sometimes', 'in:ring_all,round_robin,least_calls'], 'max_wait_seconds' => ['sometimes', 'integer', 'min:1'], 'members' => ['nullable', 'array'], 'members.*' => ['integer', 'distinct']]))], 201);
    }

    public function calls(Request $r, TelephonyQuery $q): JsonResponse
    {
        $perPage = min(100, max(1, $r->integer('per_page', 25)));

        return response()->json(['data' => $q->calls($this->team($r))->paginate($perPage)]);
    }

    public function logCall(Request $r, LogTelephonyCall $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $r->validate(['from_number' => ['required', 'string', 'max:32'], 'to_number' => ['required', 'string', 'max:32'], 'direction' => ['sometimes', 'in:inbound,outbound'], 'status' => ['sometimes', 'string', 'max:50'], 'number_id' => ['nullable', 'integer', 'min:1'], 'contact_id' => ['nullable', 'integer', 'min:1'], 'started_at' => ['nullable', 'date'], 'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'], 'metadata' => ['nullable', 'array'], 'idempotency_key' => ['required', 'string', 'max:255']]))], 201);
    }

    public function updateCall(Request $r, int $call, UpdateCall $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $call, $r->validate(['disposition' => ['nullable', 'string', 'max:100'], 'recording_url' => ['nullable', 'url', 'max:2048'], 'voicemail_url' => ['nullable', 'url', 'max:2048'], 'transfer_to' => ['nullable', 'string', 'max:32']]))]);
    }
}
