<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackagingApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\SaasPackaging\Actions\ChangeSubscriptionStatus;
use Liberu\CRM\SaasPackaging\Actions\ProvisionSubscription;
use Liberu\CRM\SaasPackaging\Actions\RecordUsage;
use Liberu\CRM\SaasPackaging\Queries\SaasQuery;

final class SaasController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function plans(SaasQuery $q)
    {
        return response()->json(['data' => $q->plans()->get()]);
    }

    public function subscription(Request $r, SaasQuery $q)
    {
        return response()->json(['data' => $q->subscription($this->team($r))]);
    }

    public function provision(Request $r, ProvisionSubscription $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function status(Request $r, string $status, ChangeSubscriptionStatus $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $status)]);
    }

    public function usage(Request $r, SaasQuery $q)
    {
        return response()->json(['data' => $q->usage($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function recordUsage(Request $r, RecordUsage $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
