<?php

declare(strict_types=1);

namespace Liberu\Foundation\OrganizationsTeamsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StatusController
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(['data' => ['type' => 'organizations-teams', 'status' => 'available', 'version' => 'v1'], 'meta' => ['request_id' => (string) $request->header('X-Request-ID', (string) str()->uuid())]]);
    }
}
