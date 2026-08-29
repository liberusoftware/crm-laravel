<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\WhiteLabel\Actions\UpdateWhiteLabelSettings;
use Liberu\CRM\WhiteLabel\Queries\WhiteLabelSettingsQuery;
use Liberu\Foundation\ApiAccess\Support\IdempotencyStore;

final class WhiteLabelController extends Controller
{
    public function show(Request $request, WhiteLabelSettingsQuery $query): JsonResponse
    {
        $settings = $query->forTeam($this->teamId($request));

        return response()->json(['data' => $this->resource($settings)]);
    }

    public function update(Request $request, UpdateWhiteLabelSettings $update, WhiteLabelSettingsQuery $query, IdempotencyStore $idempotency): JsonResponse
    {
        $data = $request->validate(['brand_name' => ['sometimes', 'nullable', 'string', 'max:255'], 'custom_domain' => ['sometimes', 'nullable', 'string', 'max:255'], 'theme' => ['sometimes', 'string', 'max:100'], 'email_settings' => ['sometimes', 'nullable', 'array'], 'application_settings' => ['sometimes', 'nullable', 'array'], 'client_experience' => ['sometimes', 'nullable', 'array'], 'provider' => ['sometimes', 'string', 'max:100'], 'show_platform_attribution' => ['sometimes', 'boolean']]);
        $replay = $this->replayIdempotent($request, $idempotency);
        if ($replay !== null) {
            return $replay;
        }
        $current = $query->forTeam($this->teamId($request));
        $updated = $update->execute($this->teamId($request), (int) $request->user()->getKey(), array_merge($current->only(['theme', 'provider', 'show_platform_attribution']), $data), $this->expectedVersion($request));
        $response = response()->json(['data' => $this->resource($updated)]);

        return $this->completeIdempotent($request, $idempotency, $response);
    }

    private function teamId(Request $request): int
    {
        $id = $request->user()?->current_team_id;
        abort_unless($id !== null, 403, 'A current team is required.');

        return (int) $id;
    }

    private function expectedVersion(Request $request): ?int
    {
        $header = $request->header('If-Match');
        if ($header === null) {
            return null;
        }

        $version = trim($header, '" W/');
        abort_unless(ctype_digit($version), 409, 'If-Match must contain a settings version.');

        return (int) $version;
    }

    private function replayIdempotent(Request $request, IdempotencyStore $idempotency): ?JsonResponse
    {
        $key = $request->header('Idempotency-Key');
        if ($key === null) {
            return null;
        }
        abort_unless(strlen($key) <= 128 && trim($key) !== '', 422, 'Idempotency-Key must be a non-empty value of 128 characters or fewer.');
        $existing = $idempotency->begin((string) $request->user()->getKey(), $key, (string) $request->getContent());
        if ($existing === null) {
            return null;
        }
        abort_if($existing->response_body === null, 409, 'The idempotent request is still being processed.');

        return response()->json(json_decode($existing->response_body, true, 512, JSON_THROW_ON_ERROR), (int) $existing->response_status);
    }

    private function completeIdempotent(Request $request, IdempotencyStore $idempotency, JsonResponse $response): JsonResponse
    {
        $key = $request->header('Idempotency-Key');
        if ($key !== null) {
            $idempotency->complete((string) $request->user()->getKey(), $key, $response->getStatusCode(), (string) $response->getContent());
        }

        return $response;
    }

    /** @return array<string, mixed> */
    private function resource(object $settings): array
    {
        return ['id' => (string) $settings->getKey(), 'type' => 'crm-white-label-settings', 'attributes' => $settings->only(['team_id', 'brand_name', 'custom_domain', 'theme', 'email_settings', 'application_settings', 'client_experience', 'provider', 'show_platform_attribution', 'version', 'created_at', 'updated_at'])];
    }
}
