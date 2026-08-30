<?php

declare(strict_types=1);

namespace Liberu\CRM\ReferralsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Referrals\Actions\CreateProgram;
use Liberu\CRM\Referrals\Actions\CreateReferral;
use Liberu\CRM\Referrals\Actions\IssueReward;
use Liberu\CRM\Referrals\Actions\QualifyReferral;
use Liberu\CRM\Referrals\Queries\ReferralQuery;

final class ReferralsController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function programs(Request $r, ReferralQuery $q)
    {
        return response()->json(['data' => $q->programs($this->team($r))->get()]);
    }

    public function program(Request $r, CreateProgram $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function referrals(Request $r, ReferralQuery $q)
    {
        return response()->json(['data' => $q->referrals($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function referral(Request $r, CreateReferral $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function qualify(Request $r, int $referral, string $status, QualifyReferral $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $referral, $status)]);
    }

    public function reward(Request $r, IssueReward $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function rewards(Request $r, ReferralQuery $q)
    {
        return response()->json(['data' => $q->rewards($this->team($r))->get()]);
    }
}
