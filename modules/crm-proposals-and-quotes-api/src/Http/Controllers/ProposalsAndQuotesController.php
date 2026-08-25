<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotesApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\ProposalsAndQuotes\Actions\AddProposalComment;
use Liberu\CRM\ProposalsAndQuotes\Actions\ChangeProposalStatus;
use Liberu\CRM\ProposalsAndQuotes\Actions\CreateProposal;
use Liberu\CRM\ProposalsAndQuotes\Actions\CreateProposalTemplate;
use Liberu\CRM\ProposalsAndQuotes\Actions\CreateProposalVersion;
use Liberu\CRM\ProposalsAndQuotes\Queries\ProposalQuery;

final class ProposalsAndQuotesController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function templates(Request $r, ProposalQuery $q)
    {
        return response()->json(['data' => $q->templates($this->team($r))->get()]);
    }

    public function template(Request $r, CreateProposalTemplate $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function proposals(Request $r, ProposalQuery $q)
    {
        return response()->json(['data' => $q->proposals($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function proposal(Request $r, CreateProposal $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function version(Request $r, CreateProposalVersion $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function status(Request $r, int $proposal, string $status, ChangeProposalStatus $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $proposal, $status)]);
    }

    public function comment(Request $r, AddProposalComment $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
