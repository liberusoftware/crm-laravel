<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagementApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\SalesEngagement\Actions\AddStep;
use Liberu\CRM\SalesEngagement\Actions\CreateSequence;
use Liberu\CRM\SalesEngagement\Actions\EnrollContact;
use Liberu\CRM\SalesEngagement\Actions\RecordEngagementEvent;
use Liberu\CRM\SalesEngagement\Actions\StopEnrollment;
use Liberu\CRM\SalesEngagement\Queries\EngagementQuery;

final class EngagementController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function sequences(Request $r, EngagementQuery $q)
    {
        return response()->json(['data' => $q->sequences($this->team($r))->get()]);
    }

    public function sequence(Request $r, CreateSequence $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function step(Request $r, AddStep $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function enrollments(Request $r, EngagementQuery $q)
    {
        return response()->json(['data' => $q->enrollments($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function enroll(Request $r, EnrollContact $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function stop(Request $r, int $enrollment, string $reason, StopEnrollment $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $enrollment, $reason)]);
    }

    public function event(Request $r, RecordEngagementEvent $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
