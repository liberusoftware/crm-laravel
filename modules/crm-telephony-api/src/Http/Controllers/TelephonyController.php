<?php

declare(strict_types=1);

namespace Liberu\CRM\TelephonyApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function numbers(Request $r, TelephonyQuery $q)
    {
        return response()->json(['data' => $q->numbers($this->team($r))->get()]);
    }

    public function queues(Request $r, TelephonyQuery $q)
    {
        return response()->json(['data' => $q->queues($this->team($r))->get()]);
    }

    public function settings(Request $r, TelephonyQuery $q)
    {
        return response()->json(['data' => $q->settings($this->team($r))]);
    }

    public function configure(Request $r, ConfigureTelephony $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())]);
    }

    public function number(Request $r, UpsertTelephonyNumber $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function queue(Request $r, CreateTelephonyQueue $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function calls(Request $r, TelephonyQuery $q)
    {
        return response()->json(['data' => $q->calls($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function logCall(Request $r, LogTelephonyCall $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function updateCall(Request $r, int $call, UpdateCall $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $call, $r->all())]);
    }
}
