<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligenceApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\RevenueIntelligence\Actions\CreateAlert;
use Liberu\CRM\RevenueIntelligence\Actions\RecordInsight;
use Liberu\CRM\RevenueIntelligence\Actions\ResolveAlert;
use Liberu\CRM\RevenueIntelligence\Queries\RevenueIntelligenceQuery;

final class RevenueIntelligenceController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function insights(Request $r, RevenueIntelligenceQuery $q)
    {
        return response()->json(['data' => $q->insights($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function insight(Request $r, RecordInsight $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function alerts(Request $r, RevenueIntelligenceQuery $q)
    {
        return response()->json(['data' => $q->alerts($this->team($r))->get()]);
    }

    public function alert(Request $r, CreateAlert $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function resolve(Request $r, int $alert, ResolveAlert $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $alert)]);
    }
}
