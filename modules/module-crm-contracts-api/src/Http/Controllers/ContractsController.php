<?php

declare(strict_types=1);

namespace Liberu\CRM\ContractsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Contracts\Actions\CreateContract;
use Liberu\CRM\Contracts\Actions\TransitionContract;
use Liberu\CRM\Contracts\Models\Contract;
use Liberu\CRM\Contracts\Queries\ContractQuery;

final class ContractsController extends Controller
{
    public function __construct(private readonly ContractQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function index(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->contracts($t)->get());
    }

    public function store(CreateContract $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, request()->all()), 201);
    }

    public function transition(Contract $contract, string $type, TransitionContract $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, $contract, $type, (string) request('status', 'completed'), (array) request('payload', [])));
    }

    public function compliance(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->complianceDates($t));
    }
}
