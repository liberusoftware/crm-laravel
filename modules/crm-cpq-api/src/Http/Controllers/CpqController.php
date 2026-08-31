<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\CPQ\Actions\PriceQuote;
use Liberu\CRM\CPQ\Actions\SubmitQuote;
use Liberu\CRM\CPQ\Models\CpqQuote;
use Liberu\CRM\CPQ\Queries\CpqQuery;
use Liberu\CRM\CPQApi\Http\Resources\CpqQuoteResource;

final class CpqController extends Controller
{
    public function __construct(private readonly CpqQuery $query) {}

    public function index(Request $request): JsonResponse
    {
        $quotes = $this->query->quotes($this->teamId())->paginate(min(max($request->integer('per_page', 25), 1), 100));

        return CpqQuoteResource::collection($quotes)->response();
    }

    public function show(int $quote): CpqQuoteResource
    {
        return new CpqQuoteResource($this->quote($quote));
    }

    private function context(): array
    {
        $u = request()->user();

        abort_unless($u !== null && (int) $u->current_team_id > 0 && (int) $u->id > 0, 403);

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function store(PriceQuote $action): JsonResponse
    {
        [$t,$u] = $this->context();
        $input = request()->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'configuration' => ['sometimes', 'array'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.quantity' => ['required', 'numeric', 'gt:0'],
            'lines.*.discount' => ['sometimes', 'numeric', 'min:0'],
        ]);

        return (new CpqQuoteResource($action->execute($t, $u, $input)))->response()->setStatusCode(201);
    }

    public function submit(int $quote, SubmitQuote $action): JsonResponse
    {
        [$t,$u] = $this->context();

        $approval = $action->execute($t, $u, $this->quote($quote));

        return response()->json(['data' => ['id' => (string) $approval->getKey(), 'type' => 'crm-cpq-approval', 'attributes' => ['status' => $approval->status, 'quote_id' => (string) $approval->quote_id]]]);
    }

    private function teamId(): int
    {
        return $this->context()[0];
    }

    private function quote(int $id): CpqQuote
    {
        return $this->query->quotes($this->teamId())->findOrFail($id);
    }
}
