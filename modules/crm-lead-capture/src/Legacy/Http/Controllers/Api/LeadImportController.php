<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Services\LeadImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class LeadImportController extends Controller
{
    public function template(): StreamedResponse
    {
        return response()->streamDownload(static function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'last_name', 'email', 'phone', 'company', 'source', 'message', 'external_key', 'pipeline_id', 'stage_id']);
            fputcsv($handle, ['Jane Smith', 'Smith', 'jane@example.com', '+441234567890', 'Example Ltd', 'website', 'Interested in a demo', 'lead-001', '', '']);
            fclose($handle);
        }, 'lead-import-template.csv', ['Content-Type' => 'text/csv']);
    }

    public function store(Request $request, LeadImportService $importer): JsonResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);
        $team = Team::query()->find((int) $user->current_team_id);
        abort_unless($team instanceof Team, 403);
        abort_unless($team->user_id === $user->id || $team->users()->whereKey($user->id)->wherePivotIn('role', ['owner', 'admin', 'manager', 'marketing', 'sales'])->exists(), 403);
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:10240']]);

        try {
            return response()->json($importer->import($request->file('file'), (int) $team->id, (int) $user->id));
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }
}
