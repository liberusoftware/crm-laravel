<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Attribution\Actions\RecordConversion;
use Liberu\CRM\Attribution\Actions\RecordTouchpoint;
use Liberu\CRM\Attribution\Queries\AttributionQuery;

final class AttributionController extends Controller
{
    public function __construct(private readonly AttributionQuery $query) {}

    private function teamId(): int
    {
        return (int) request()->user()->current_team_id;
    }

    public function touchpoints(): JsonResponse
    {
        return response()->json($this->query->touchpoints($this->teamId())->paginate(50));
    }

    public function recordTouchpoint(RecordTouchpoint $action): JsonResponse
    {
        return response()->json($action->execute($this->teamId(), request()->all()), 201);
    }

    public function conversions(): JsonResponse
    {
        return response()->json($this->query->conversions($this->teamId())->paginate(50));
    }

    public function recordConversion(RecordConversion $action): JsonResponse
    {
        return response()->json($action->execute($this->teamId(), request()->all()), 201);
    }
}
