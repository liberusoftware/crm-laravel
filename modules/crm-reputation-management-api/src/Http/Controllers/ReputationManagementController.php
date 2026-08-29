<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagementApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\ReputationManagement\Actions\ConnectReviewSite;
use Liberu\CRM\ReputationManagement\Actions\CreateReviewRequest;
use Liberu\CRM\ReputationManagement\Actions\RecordReview;
use Liberu\CRM\ReputationManagement\Actions\RespondToReview;
use Liberu\CRM\ReputationManagement\Actions\SaveTemplate;
use Liberu\CRM\ReputationManagement\Queries\ReputationQuery;

final class ReputationManagementController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function connections(Request $r, ReputationQuery $q)
    {
        return response()->json(['data' => $q->connections($this->team($r))->get()]);
    }

    public function connection(Request $r, ConnectReviewSite $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function requests(Request $r, ReputationQuery $q)
    {
        return response()->json(['data' => $q->requests($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function request(Request $r, CreateReviewRequest $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function reviews(Request $r, ReputationQuery $q)
    {
        return response()->json(['data' => $q->reviews($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function review(Request $r, RecordReview $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function respond(Request $r, int $review, RespondToReview $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $review, $r->all())]);
    }

    public function template(Request $r, SaveTemplate $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
