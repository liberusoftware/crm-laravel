<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlementsApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\SlaAndEntitlements\Actions\CreateCalendar;
use Liberu\CRM\SlaAndEntitlements\Actions\CreateContract;
use Liberu\CRM\SlaAndEntitlements\Actions\EvaluateCase;
use Liberu\CRM\SlaAndEntitlements\Actions\OpenCase;
use Liberu\CRM\SlaAndEntitlements\Actions\RequestException;
use Liberu\CRM\SlaAndEntitlements\Actions\SetEntitlement;
use Liberu\CRM\SlaAndEntitlements\Actions\TransitionCase;
use Liberu\CRM\SlaAndEntitlements\Queries\SlaQuery;

final class SlaController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function contracts(Request $r, SlaQuery $q)
    {
        return response()->json(['data' => $q->contracts($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function calendars(Request $r, SlaQuery $q)
    {
        return response()->json(['data' => $q->calendars($this->team($r))->get()]);
    }

    public function entitlements(Request $r, SlaQuery $q)
    {
        return response()->json(['data' => $q->entitlements($this->team($r))->get()]);
    }

    public function cases(Request $r, SlaQuery $q)
    {
        return response()->json(['data' => $q->cases($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function storeContract(Request $r, CreateContract $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function storeCalendar(Request $r, CreateCalendar $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function storeEntitlement(Request $r, SetEntitlement $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function storeCase(Request $r, OpenCase $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function transition(Request $r, int $case, string $transition, TransitionCase $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $case, $transition, $r->all())]);
    }

    public function evaluate(Request $r, int $case, EvaluateCase $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $case)]);
    }

    public function exception(Request $r, RequestException $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
