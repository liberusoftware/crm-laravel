<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\WebIntent\Actions\ConvertIntent;
use Liberu\CRM\WebIntent\Actions\CreateAlert;
use Liberu\CRM\WebIntent\Actions\IdentifyAccount;
use Liberu\CRM\WebIntent\Actions\RecordEngagement;
use Liberu\CRM\WebIntent\Actions\RecordVisit;
use Liberu\CRM\WebIntent\Actions\ResolveAlert;
use Liberu\CRM\WebIntent\Actions\SetConsent;
use Liberu\CRM\WebIntent\Models\WebIntentAlert;
use Liberu\CRM\WebIntent\Models\WebIntentVisit;
use Liberu\CRM\WebIntent\Queries\WebIntentQuery;
use Liberu\CRM\WebIntent\Services\WebIntentPolicy;
use Liberu\CRM\WebIntent\Services\WebIntentScorer;

final class WebIntentController extends Controller
{
    public function index(Request $request, WebIntentQuery $query): JsonResponse
    {
        $visits = $query->visits($this->teamId($request))->when($request->query('intent_level'), fn ($builder, $level) => $builder->where('intent_level', $level))->latest()->paginate(min(max((int) $request->query('page[size]', 25), 1), 100));

        return response()->json(['data' => $visits->through(fn (WebIntentVisit $visit): array => $this->visit($visit)), 'meta' => ['current_page' => $visits->currentPage(), 'last_page' => $visits->lastPage()], 'links' => ['self' => $request->fullUrl()]]);
    }

    public function store(Request $request, RecordVisit $record): JsonResponse
    {
        $data = $request->validate(['visitor_key' => ['required', 'string', 'max:128'], 'session_key' => ['nullable', 'string', 'max:128'], 'landing_url' => ['nullable', 'url', 'max:2048'], 'referrer' => ['nullable', 'url', 'max:2048'], 'consent_status' => ['nullable', 'in:unknown,granted,denied,withdrawn'], 'metadata' => ['nullable', 'array']]);
        $visitorKey = (string) $data['visitor_key'];
        unset($data['visitor_key']);
        $visit = $record->execute($this->teamId($request), $visitorKey, $data);

        return response()->json(['data' => $this->visit($visit)], 201);
    }

    public function show(Request $request, int $visit): JsonResponse
    {
        return response()->json(['data' => $this->visit(WebIntentVisit::query()->where('team_id', $this->teamId($request))->findOrFail($visit))]);
    }

    public function engagement(Request $request, int $visit, RecordEngagement $record, WebIntentScorer $scorer): JsonResponse
    {
        $item = WebIntentVisit::query()->where('team_id', $this->teamId($request))->findOrFail($visit);
        $data = $request->validate(['event_type' => ['required', 'string', 'max:64'], 'page_url' => ['nullable', 'url', 'max:2048'], 'content_type' => ['nullable', 'string', 'max:100'], 'content_id' => ['nullable', 'string', 'max:190'], 'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'], 'dedupe_key' => ['nullable', 'string', 'max:190'], 'metadata' => ['nullable', 'array']]);

        return response()->json(['data' => $record->execute($item, $data, $scorer)->only(['id', 'visit_id', 'event_type', 'points', 'occurred_at'])], 201);
    }

    public function consent(Request $request, SetConsent $set): JsonResponse
    {
        $data = $request->validate(['visitor_key' => ['required', 'string', 'max:128'], 'purpose' => ['required', 'string', 'max:100'], 'status' => ['required', 'in:granted,denied,withdrawn'], 'policy_version' => ['nullable', 'string', 'max:64']]);
        $consent = $set->execute($this->teamId($request), $data['visitor_key'], $data['purpose'], $data['status'], $data['policy_version'] ?? null);

        return response()->json(['data' => $consent->only(['id', 'visitor_key', 'purpose', 'status', 'policy_version', 'granted_at', 'revoked_at'])]);
    }

    public function identification(Request $request, IdentifyAccount $identify, WebIntentPolicy $policy): JsonResponse
    {
        $data = $request->validate(['visitor_key' => ['required', 'string', 'max:128'], 'adapter' => ['required', 'string', 'max:100'], 'account_name' => ['nullable', 'string', 'max:255'], 'account_domain' => ['nullable', 'string', 'max:255'], 'confidence' => ['required', 'integer', 'between:0,100'], 'metadata' => ['nullable', 'array']]);
        $record = $identify->execute($this->teamId($request), (int) $request->user()->getKey(), $data['visitor_key'], $data['adapter'], $data['account_name'] ?? null, $data['account_domain'] ?? null, (int) $data['confidence'], $data['metadata'] ?? [], $policy);

        return response()->json(['data' => $record->only(['id', 'visitor_key', 'adapter', 'account_name', 'account_domain', 'confidence', 'status', 'metadata'])], 201);
    }

    public function alerts(Request $request, WebIntentQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->alerts($this->teamId($request))->latest()->paginate(25)->through(fn (WebIntentAlert $alert): array => $alert->only(['id', 'visitor_key', 'visit_id', 'severity', 'title', 'details', 'status', 'triggered_at', 'resolved_at']))]);
    }

    public function createAlert(Request $request, CreateAlert $create, WebIntentPolicy $policy): JsonResponse
    {
        $data = $request->validate(['visitor_key' => ['required', 'string', 'max:128'], 'visit_id' => ['nullable', 'integer', 'min:1'], 'title' => ['required', 'string', 'max:255'], 'details' => ['nullable', 'string'], 'severity' => ['required', 'in:low,normal,high,critical']]);
        $alert = $create->execute($this->teamId($request), (int) $request->user()->getKey(), $data['visitor_key'], $data['title'], $data['severity'], $data['visit_id'] ?? null, $data['details'] ?? null, $policy);

        return response()->json(['data' => $alert->only(['id', 'visitor_key', 'visit_id', 'severity', 'title', 'details', 'status', 'triggered_at'])], 201);
    }

    public function resolveAlert(Request $request, int $alert, ResolveAlert $resolve, WebIntentPolicy $policy): JsonResponse
    {
        $record = WebIntentAlert::query()->where('team_id', $this->teamId($request))->findOrFail($alert);

        return response()->json(['data' => $resolve->execute($record, (int) $request->user()->getKey(), $policy)->only(['id', 'status', 'resolved_at', 'resolved_by'])]);
    }

    public function convert(Request $request, ConvertIntent $convert, WebIntentPolicy $policy): JsonResponse
    {
        $data = $request->validate(['visitor_key' => ['required', 'string', 'max:128'], 'visit_id' => ['nullable', 'integer', 'min:1'], 'target_type' => ['required', 'string', 'max:100'], 'target_id' => ['required', 'integer', 'min:1'], 'metadata' => ['nullable', 'array']]);
        $record = $convert->execute($this->teamId($request), (int) $request->user()->getKey(), $data['visitor_key'], $data['target_type'], (int) $data['target_id'], $data['visit_id'] ?? null, $data['metadata'] ?? [], $policy);

        return response()->json(['data' => $record->only(['id', 'visitor_key', 'visit_id', 'target_type', 'target_id', 'status', 'created_at'])], 201);
    }

    public function summary(Request $request, WebIntentQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->summary($this->teamId($request))]);
    }

    private function teamId(Request $request): int
    {
        $id = $request->user()?->current_team_id;
        abort_unless($id !== null, 403, 'A current team is required.');

        return (int) $id;
    }

    /** @return array<string, mixed> */
    private function visit(WebIntentVisit $visit): array
    {
        return ['id' => (string) $visit->getKey(), 'type' => 'crm-web-intent-visit', 'attributes' => $visit->only(['team_id', 'visitor_key', 'session_key', 'landing_url', 'referrer', 'consent_status', 'score', 'intent_level', 'status', 'started_at', 'ended_at', 'metadata', 'created_at', 'updated_at'])];
    }
}
