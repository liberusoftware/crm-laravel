<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalyticsApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\ServiceAnalytics\Actions\RecordMetric;
use Liberu\CRM\ServiceAnalytics\Actions\RecordServiceMetrics;
use Liberu\CRM\ServiceAnalytics\Queries\AnalyticsQuery;

final class AnalyticsController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function index(Request $r, AnalyticsQuery $q)
    {
        $team = $this->team($r);

        return response()->json(['data' => $q->snapshots($team)->paginate((int) $r->integer('per_page', 50))]);
    }

    public function metric(Request $r, string $metric, AnalyticsQuery $q)
    {
        $team = $this->team($r);

        return response()->json(['data' => $q->metric($team, $metric, $r->date('from'), $r->date('to'))->get()]);
    }

    public function summary(Request $r, AnalyticsQuery $q)
    {
        return response()->json(['data' => $q->summary($this->team($r), $r->date('from'), $r->date('to'))]);
    }

    public function store(Request $r, RecordMetric $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function batch(Request $r, RecordServiceMetrics $a)
    {
        return response()->json(['recorded' => $a->execute($this->team($r), (int) $r->user()->id, $r->input('metrics', []))], 201);
    }
}
