<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelinesApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\SalesPipelines\Actions\CloseOpportunity;
use Liberu\CRM\SalesPipelines\Actions\CreateOpportunity;
use Liberu\CRM\SalesPipelines\Actions\CreatePipeline;
use Liberu\CRM\SalesPipelines\Actions\CreateStage;
use Liberu\CRM\SalesPipelines\Actions\MoveOpportunity;
use Liberu\CRM\SalesPipelines\Queries\PipelineQuery;

final class PipelineController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function pipelines(Request $r, PipelineQuery $q)
    {
        return response()->json(['data' => $q->pipelines($this->team($r))->get()]);
    }

    public function pipeline(Request $r, CreatePipeline $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function stage(Request $r, CreateStage $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function opportunities(Request $r, PipelineQuery $q)
    {
        return response()->json(['data' => $q->opportunities($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function opportunity(Request $r, CreateOpportunity $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function move(Request $r, int $opportunity, MoveOpportunity $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $opportunity, $r->all())]);
    }

    public function close(Request $r, int $opportunity, string $status, CloseOpportunity $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $opportunity, $status, $r->input('loss_reason'))]);
    }
}
