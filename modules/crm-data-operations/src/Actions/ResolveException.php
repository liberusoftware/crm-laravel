<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\DataOperations\Models\OperationException;

final class ResolveException
{
    public function execute(OperationException $exception, int $actorId): OperationException
    {
        DB::transaction(function () use ($exception, $actorId): void {
            $exception->update(['status' => 'resolved', 'resolved_by' => $actorId, 'resolved_at' => now()]);
        });

        return $exception->refresh();
    }
}
