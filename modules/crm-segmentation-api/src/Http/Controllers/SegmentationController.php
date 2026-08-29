<?php

declare(strict_types=1);

namespace Liberu\CRM\SegmentationApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\Segmentation\Actions\CreateAudience;
use Liberu\CRM\Segmentation\Actions\RecordBehaviorEvent;
use Liberu\CRM\Segmentation\Actions\RefreshAudience;
use Liberu\CRM\Segmentation\Actions\UpdateAudience;
use Liberu\CRM\Segmentation\Queries\SegmentationQuery;

final class SegmentationController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function index(Request $r, SegmentationQuery $q)
    {
        return response()->json(['data' => $q->audiences($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function store(Request $r, CreateAudience $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function update(Request $r, int $audience, UpdateAudience $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $audience, $r->all())]);
    }

    public function members(Request $r, int $audience, SegmentationQuery $q)
    {
        return response()->json(['data' => $q->members($this->team($r), $audience)->paginate((int) $r->integer('per_page', 50))]);
    }

    public function refresh(Request $r, int $audience, RefreshAudience $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $audience, $r->all())]);
    }

    public function event(Request $r, RecordBehaviorEvent $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
