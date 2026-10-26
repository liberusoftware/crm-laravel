<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionApi\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\Attribution\Actions\RecordConversion;
use Liberu\CRM\Attribution\Actions\RecordTouchpoint;
use Liberu\CRM\Attribution\Queries\AttributionQuery;

final class AttributionController extends Controller
{
    public function __construct(private readonly AttributionQuery $query) {}

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403, 'A current team is required.');

        return (int) $teamId;
    }

    public function touchpoints(Request $request): JsonResponse
    {
        $pageSize = min(max((int) $request->query('page[size]', 25), 1), 100);
        $touchpoints = $this->query->touchpoints($this->teamId($request))->paginate($pageSize);

        return response()->json([
            'data' => $touchpoints->through(fn ($touchpoint): array => $this->touchpointResource($touchpoint)),
            'meta' => ['current_page' => $touchpoints->currentPage(), 'last_page' => $touchpoints->lastPage()],
            'links' => ['self' => $request->fullUrl()],
        ]);
    }

    public function recordTouchpoint(Request $request, RecordTouchpoint $action): JsonResponse
    {
        $data = $request->validate(['visitor_key' => ['required', 'string', 'max:255'], 'source' => ['required', 'string', 'max:120'], 'medium' => ['nullable', 'string', 'max:120'], 'campaign' => ['nullable', 'string', 'max:180'], 'content' => ['nullable', 'string', 'max:180'], 'term' => ['nullable', 'string', 'max:180'], 'click_id' => ['nullable', 'string', 'max:255'], 'channel' => ['nullable', 'string', 'max:80'], 'cost' => ['nullable', 'numeric', 'min:0'], 'metadata' => ['nullable', 'array'], 'occurred_at' => ['nullable', 'date']]);

        return response()->json(['data' => $this->touchpointResource($action->execute($this->teamId($request), $data))], 201);
    }

    public function conversions(Request $request): JsonResponse
    {
        $pageSize = min(max((int) $request->query('page[size]', 25), 1), 100);
        $conversions = $this->query->conversions($this->teamId($request))->paginate($pageSize);

        return response()->json([
            'data' => $conversions->through(fn ($conversion): array => $this->conversionResource($conversion)),
            'meta' => ['current_page' => $conversions->currentPage(), 'last_page' => $conversions->lastPage()],
            'links' => ['self' => $request->fullUrl()],
        ]);
    }

    public function recordConversion(Request $request, RecordConversion $action): JsonResponse
    {
        $data = $request->validate(['visitor_key' => ['required', 'string', 'max:255'], 'conversion_key' => ['required', 'string', 'max:180'], 'model' => ['nullable', 'in:first_touch,last_touch,linear,multi_touch'], 'value' => ['nullable', 'numeric', 'min:0'], 'converted_at' => ['nullable', 'date']]);

        return response()->json(['data' => $this->conversionResource($action->execute($this->teamId($request), $data))], 201);
    }

    /** @return array<string, mixed> */
    private function touchpointResource(Model $touchpoint): array
    {
        return ['id' => (string) $touchpoint->getKey(), 'type' => 'crm-attribution-touchpoint', 'attributes' => $touchpoint->only(['visitor_key', 'source', 'medium', 'campaign', 'content', 'term', 'click_id', 'channel', 'cost', 'occurred_at', 'metadata', 'created_at'])];
    }

    /** @return array<string, mixed> */
    private function conversionResource(Model $conversion): array
    {
        return ['id' => (string) $conversion->getKey(), 'type' => 'crm-attribution-conversion', 'attributes' => $conversion->only(['visitor_key', 'conversion_key', 'model', 'value', 'allocations', 'converted_at', 'created_at'])];
    }
}
