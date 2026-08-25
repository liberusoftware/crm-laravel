<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanningApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\ResourcePlanning\Actions\CreateBooking;
use Liberu\CRM\ResourcePlanning\Actions\RecordForecast;
use Liberu\CRM\ResourcePlanning\Actions\SetCapacity;
use Liberu\CRM\ResourcePlanning\Actions\SetRate;
use Liberu\CRM\ResourcePlanning\Actions\UpsertSkill;
use Liberu\CRM\ResourcePlanning\Queries\ResourcePlanningQuery;

final class ResourcePlanningController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function skills(Request $r, ResourcePlanningQuery $q)
    {
        return response()->json(['data' => $q->skills($this->team($r))->get()]);
    }

    public function skill(Request $r, UpsertSkill $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function capacity(Request $r, ResourcePlanningQuery $q)
    {
        return response()->json(['data' => $q->capacity($this->team($r))->get()]);
    }

    public function setCapacity(Request $r, SetCapacity $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function bookings(Request $r, ResourcePlanningQuery $q)
    {
        return response()->json(['data' => $q->bookings($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function booking(Request $r, CreateBooking $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function rate(Request $r, SetRate $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function forecast(Request $r, RecordForecast $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
