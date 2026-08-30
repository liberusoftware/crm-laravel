<?php

declare(strict_types=1);

namespace Liberu\CRM\SchedulingApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\Scheduling\Actions\ChangeBookingStatus;
use Liberu\CRM\Scheduling\Actions\CreateBooking;
use Liberu\CRM\Scheduling\Actions\CreateSchedulingLink;
use Liberu\CRM\Scheduling\Queries\SchedulingQuery;

final class SchedulingController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function links(Request $r, SchedulingQuery $q)
    {
        return response()->json(['data' => $q->links($this->team($r))->get()]);
    }

    public function storeLink(Request $r, CreateSchedulingLink $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function bookings(Request $r, SchedulingQuery $q)
    {
        return response()->json(['data' => $q->bookings($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function storeBooking(Request $r, CreateBooking $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function status(Request $r, int $booking, string $status, ChangeBookingStatus $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $booking, $status, $r->all())]);
    }
}
