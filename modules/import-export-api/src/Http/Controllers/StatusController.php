<?php

declare(strict_types=1);

namespace Liberu\Foundation\ImportExportApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StatusController
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(['data' => ['type' => 'import-export', 'status' => 'available', 'version' => 'v1'], 'meta' => ['request_id' => (string) $request->header('X-Request-ID', (string) str()->uuid())]]);
    }
}
