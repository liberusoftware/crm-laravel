<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\AgencyWorkspace\Actions\CreateAgencyAccount;
use Liberu\CRM\AgencyWorkspace\Actions\GrantAgencyAccess;
use Liberu\CRM\AgencyWorkspace\Actions\UpdateAgencyUsage;
use Liberu\CRM\AgencyWorkspace\Models\AgencyAccount;
use Liberu\CRM\AgencyWorkspace\Queries\AgencyQuery;

final class AgencyWorkspaceController extends Controller
{
    public function __construct(private readonly AgencyQuery $query) {}

    private function context(Request $request): array
    {
        $user = $request->user();

        abort_unless($user !== null, 401);

        $teamId = (int) $user->getAttribute('current_team_id');
        abort_unless($teamId > 0, 403);

        return [$teamId, (int) $user->getKey()];
    }

    public function index(Request $request): JsonResponse
    {
        [$teamId] = $this->context($request);
        $accounts = $this->query->accounts($teamId)
            ->paginate(min($request->integer('per_page', 25), 100));

        return response()->json([
            'data' => $accounts->getCollection()->map(fn (AgencyAccount $account): array => $this->account($account))->values(),
            'meta' => [
                'current_page' => $accounts->currentPage(),
                'last_page' => $accounts->lastPage(),
                'per_page' => $accounts->perPage(),
                'total' => $accounts->total(),
            ],
            'links' => [
                'first' => $accounts->url(1),
                'last' => $accounts->url($accounts->lastPage()),
                'next' => $accounts->nextPageUrl(),
                'prev' => $accounts->previousPageUrl(),
            ],
        ]);
    }

    public function store(Request $request, CreateAgencyAccount $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        return response()->json(['data' => $this->account($action->execute($teamId, $actorId, $request->all()))], 201);
    }

    public function grantAccess(Request $request, AgencyAccount $account, GrantAgencyAccess $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        $access = $action->execute($teamId, $actorId, $account, $request->all());

        return response()->json(['data' => $access->toArray()], 201);
    }

    public function usage(Request $request, AgencyAccount $account, UpdateAgencyUsage $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        return response()->json(['data' => $this->account($action->execute($teamId, $actorId, $account, $request->all()))]);
    }

    /** @return array<string,mixed> */
    private function account(AgencyAccount $account): array
    {
        return [
            'id' => $account->getKey(),
            'team_id' => $account->team_id,
            'parent_id' => $account->getAttribute('parent_id'),
            'owner_id' => $account->getAttribute('owner_id'),
            'name' => $account->getAttribute('name'),
            'account_type' => $account->getAttribute('account_type'),
            'status' => $account->getAttribute('status'),
            'branding' => $account->getAttribute('branding'),
            'usage_snapshot' => $account->getAttribute('usage_snapshot'),
            'created_at' => $account->getAttribute('created_at'),
            'updated_at' => $account->getAttribute('updated_at'),
        ];
    }
}
