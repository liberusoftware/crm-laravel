<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\ConsentAndPreferences\Actions\GrantConsent;
use Liberu\CRM\ConsentAndPreferences\Actions\SetPreference;
use Liberu\CRM\ConsentAndPreferences\Actions\WithdrawConsent;
use Liberu\CRM\ConsentAndPreferences\Models\ConsentRecord;
use Liberu\CRM\ConsentAndPreferences\Services\PolicyEvaluator;

final class ConsentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = $this->owned($request)->latest()->paginate(min(max((int) $request->query('page[size]', 25), 1), 100));

        return response()->json(['data' => $records->through(fn (ConsentRecord $record): array => $this->resource($record)), 'meta' => ['current_page' => $records->currentPage(), 'last_page' => $records->lastPage()]]);
    }

    public function store(Request $request, GrantConsent $grant, SetPreference $preference): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string', 'max:120'], 'subject_id' => ['required', 'integer', 'min:1'], 'channel' => ['required', 'string', 'max:40'], 'topic' => ['nullable', 'string', 'max:120'], 'lawful_basis' => ['required', 'in:consent,contract,legal_obligation,vital_interests,public_task,legitimate_interest'], 'source' => ['required', 'string', 'max:120'], 'proof' => ['required', 'array'], 'expires_at' => ['nullable', 'date', 'after:now'], 'preference_state' => ['nullable', 'in:allowed,denied'], 'quiet_hours' => ['nullable', 'array'], 'timezone' => ['nullable', 'timezone']]);
        $teamId = (int) $request->user()->current_team_id;
        $record = $grant->execute($teamId, $data['subject_type'], (int) $data['subject_id'], $data, $request->user()->getKey());
        if (isset($data['preference_state']) || isset($data['quiet_hours'])) {
            $preference->execute($teamId, $record->subject_type, $record->subject_id, $record->channel, $record->topic, ['state' => $data['preference_state'] ?? 'allowed', 'quiet_hours' => $data['quiet_hours'] ?? null, 'timezone' => $data['timezone'] ?? 'UTC'], $request->user()->getKey());
        }

        return response()->json(['data' => $this->resource($record)], 201);
    }

    public function show(Request $request, int $consent): JsonResponse
    {
        return response()->json(['data' => $this->resource($this->owned($request)->findOrFail($consent))]);
    }

    public function update(Request $request, int $consent): JsonResponse
    {
        $record = $this->owned($request)->findOrFail($consent);
        $record->update($request->validate(['expires_at' => ['sometimes', 'nullable', 'date', 'after:now'], 'proof' => ['sometimes', 'array']]));

        return response()->json(['data' => $this->resource($record->refresh())]);
    }

    public function withdraw(Request $request, int $consent, WithdrawConsent $withdraw): JsonResponse
    {
        return response()->json(['data' => $this->resource($withdraw->execute($this->owned($request)->findOrFail($consent)))]);
    }

    public function evaluate(Request $request, PolicyEvaluator $evaluator): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string', 'max:120'], 'subject_id' => ['required', 'integer', 'min:1'], 'channel' => ['required', 'string', 'max:40'], 'topic' => ['nullable', 'string', 'max:120']]);
        $result = $evaluator->evaluate((int) $request->user()->current_team_id, $data['subject_type'], (int) $data['subject_id'], $data['channel'], $data['topic'] ?? 'general');

        return response()->json(['data' => ['allowed' => $result['allowed'], 'reasons' => $result['reasons'], 'evaluation_id' => (string) $result['evaluation']->getKey()]]);
    }

    private function owned(Request $request)
    {
        return ConsentRecord::query()->where('team_id', (int) $request->user()->current_team_id);
    }

    /** @return array<string, mixed> */
    private function resource(ConsentRecord $record): array
    {
        return ['id' => (string) $record->getKey(), 'type' => 'crm-consent-and-preferences', 'attributes' => $record->only(['subject_type', 'subject_id', 'channel', 'topic', 'lawful_basis', 'status', 'source', 'proof', 'consented_at', 'expires_at', 'withdrawn_at', 'created_at'])];
    }
}
