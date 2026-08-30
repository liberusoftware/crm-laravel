<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadCapture\Models\LeadCapture;

final class UpdateCaptureStatus
{
    public function execute(LeadCapture $capture, string $status, ?string $failureReason = null): LeadCapture
    {
        if (! in_array($status, ['received', 'processing', 'converted', 'rejected', 'failed'], true)) {
            throw ValidationException::withMessages(['status' => 'Unsupported capture status.']);
        }
        DB::transaction(fn (): bool => $capture->update(['status' => $status, 'failure_reason' => $failureReason, 'processed_at' => in_array($status, ['converted', 'rejected', 'failed'], true) ? now() : null, 'updated_at' => now()]));

        return $capture->refresh();
    }
}
