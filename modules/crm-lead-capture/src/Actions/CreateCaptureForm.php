<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadCapture\Models\CaptureForm;

final class CreateCaptureForm
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, ?int $actorId, array $attributes): CaptureForm
    {
        $kind = (string) ($attributes['kind'] ?? 'form');
        if (! in_array($kind, ['form', 'survey'], true)) {
            throw ValidationException::withMessages(['kind' => 'A capture form must be a form or survey.']);
        }
        if (! is_array($attributes['schema'] ?? null) || $attributes['schema'] === []) {
            throw ValidationException::withMessages(['schema' => 'At least one field definition is required.']);
        }

        return DB::transaction(fn (): CaptureForm => CaptureForm::query()->create(array_merge($attributes, ['team_id' => $teamId, 'actor_id' => $actorId, 'kind' => $kind, 'status' => $attributes['status'] ?? 'draft']))->refresh());
    }
}
