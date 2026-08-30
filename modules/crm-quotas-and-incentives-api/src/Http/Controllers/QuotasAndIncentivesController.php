<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentivesApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\QuotasAndIncentives\Actions\CreateCommissionPlan;
use Liberu\CRM\QuotasAndIncentives\Actions\CreateQuota;
use Liberu\CRM\QuotasAndIncentives\Actions\CreditCommission;
use Liberu\CRM\QuotasAndIncentives\Actions\ExportCommissions;
use Liberu\CRM\QuotasAndIncentives\Actions\OpenDispute;
use Liberu\CRM\QuotasAndIncentives\Actions\ResolveDispute;
use Liberu\CRM\QuotasAndIncentives\Queries\QuotaQuery;

final class QuotasAndIncentivesController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function quotas(Request $r, QuotaQuery $q)
    {
        return response()->json(['data' => $q->quotas($this->team($r))->get()]);
    }

    public function quota(Request $r, CreateQuota $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function plans(Request $r, QuotaQuery $q)
    {
        return response()->json(['data' => $q->plans($this->team($r))->get()]);
    }

    public function plan(Request $r, CreateCommissionPlan $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function credits(Request $r, QuotaQuery $q)
    {
        return response()->json(['data' => $q->credits($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function credit(Request $r, CreditCommission $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function dispute(Request $r, OpenDispute $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function resolve(Request $r, int $dispute, ResolveDispute $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $dispute, $r->all())]);
    }

    public function export(Request $r, ExportCommissions $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 202);
    }
}
