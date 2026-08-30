<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadCapture\Events\CaptureFormSubmitted;
use Liberu\CRM\LeadCapture\Models\CaptureForm;
use Liberu\CRM\LeadCapture\Models\LeadCapture;

final class SubmitCaptureForm
{
    /** @param array<string, mixed> $payload */
    public function execute(CaptureForm $form, ?int $actorId, array $payload): LeadCapture
    {
        if ($form->status !== 'published') {
            throw ValidationException::withMessages(['form' => 'This capture form is not published.']);
        }
        $fields = collect($form->schema);
        foreach ($fields->where('required', true) as $field) {
            $name = (string) ($field['name'] ?? '');
            if ($name === '' || blank($payload[$name] ?? null)) {
                throw ValidationException::withMessages([$name !== '' ? $name : 'payload' => 'This field is required.']);
            }
        }

        return DB::transaction(function () use ($form, $actorId, $payload): LeadCapture {
            $capture = app(CaptureLead::class)->executeLegacy($form->team_id, $actorId, ['kind' => $form->kind, 'name' => $payload['name'] ?? null, 'email' => $payload['email'] ?? null, 'phone' => $payload['phone'] ?? null, 'source' => 'capture_form', 'payload' => $payload, 'provenance' => ['form_id' => $form->getKey()]]);
            $form->increment('submissions_count');
            CaptureFormSubmitted::dispatch($capture);

            return $capture;
        });
    }
}
