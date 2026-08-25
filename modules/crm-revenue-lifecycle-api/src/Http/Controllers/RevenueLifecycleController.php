<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycleApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\RevenueLifecycle\Actions\CreateOrder;
use Liberu\CRM\RevenueLifecycle\Actions\ManageAsset;
use Liberu\CRM\RevenueLifecycle\Actions\RecordUsageSignal;
use Liberu\CRM\RevenueLifecycle\Actions\ResolveFallout;
use Liberu\CRM\RevenueLifecycle\Queries\RevenueQuery;

final class RevenueLifecycleController extends Controller
{
    private function team(Request $request): int
    {
        abort_unless($request->user()?->current_team_id !== null, 403);

        return (int) $request->user()->current_team_id;
    }

    public function assets(Request $request, RevenueQuery $query)
    {
        return response()->json(['data' => $query->assets($this->team($request))->get()]);
    }

    public function asset(Request $request, ManageAsset $action)
    {
        return response()->json(['data' => $action->execute($this->team($request), (int) $request->user()->id, $request->all())], 201);
    }

    public function order(Request $request, CreateOrder $action)
    {
        return response()->json(['data' => $action->execute($this->team($request), (int) $request->user()->id, $request->all())], 201);
    }

    public function usage(Request $request, int $asset, RecordUsageSignal $action)
    {
        return response()->json(['data' => $action->execute($this->team($request), (int) $request->user()->id, $asset, $request->all())]);
    }

    public function fallout(Request $request, RevenueQuery $query)
    {
        return response()->json(['data' => $query->fallout($this->team($request))->get()]);
    }

    public function resolveFallout(Request $request, int $fallout, ResolveFallout $action)
    {
        return response()->json(['data' => $action->execute($this->team($request), (int) $request->user()->id, $fallout, $request->all())]);
    }
}
