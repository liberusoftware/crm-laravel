<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\DataOperations\Models\DataOperation;

final class TransitionDataOperation
{
    /** @var array<string, array<int, string>> */
    private const TRANSITIONS = ['draft' => ['queued'], 'queued' => ['running', 'failed'], 'running' => ['completed', 'failed', 'partial'], 'partial' => ['queued', 'completed', 'failed'], 'failed' => ['queued']];

    public function execute(DataOperation $operation, string $status, ?string $reason = null): DataOperation
    {
        if (! in_array($status, self::TRANSITIONS[$operation->status] ?? [], true)) {
            throw ValidationException::withMessages(['status' => "Cannot transition {$operation->status} to {$status}."]);
        }

        return DB::transaction(function () use ($operation, $status, $reason): DataOperation {
            $values = ['status' => $status, 'failure_reason' => $reason];
            if ($status === 'running') {
                $values['started_at'] = now();
            }
            if (in_array($status, ['completed', 'failed'], true)) {
                $values['completed_at'] = now();
            }
            $operation->update($values);

            return $operation->refresh();
        });
    }
}
