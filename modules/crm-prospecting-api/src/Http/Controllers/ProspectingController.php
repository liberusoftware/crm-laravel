<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Prospecting\Actions\CreateIdealCustomerProfile;
use Liberu\CRM\Prospecting\Actions\CreateProspectSearch;
use Liberu\CRM\Prospecting\Actions\ImportProspect;
use Liberu\CRM\Prospecting\Actions\QueueExport;
use Liberu\CRM\Prospecting\Actions\QueueResearch;
use Liberu\CRM\Prospecting\Actions\RevealContact;
use Liberu\CRM\Prospecting\Queries\ProspectingQuery;

final class ProspectingController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function profiles(Request $r, ProspectingQuery $q)
    {
        return response()->json(['data' => $q->profiles($this->team($r))->get()]);
    }

    public function profile(Request $r, CreateIdealCustomerProfile $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function searches(Request $r, ProspectingQuery $q)
    {
        return response()->json(['data' => $q->searches($this->team($r))->get()]);
    }

    public function search(Request $r, CreateProspectSearch $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function prospects(Request $r, ProspectingQuery $q)
    {
        return response()->json(['data' => $q->prospects($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function prospect(Request $r, ImportProspect $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function research(Request $r, QueueResearch $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function reveal(Request $r, RevealContact $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function export(Request $r, QueueExport $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 202);
    }
}
