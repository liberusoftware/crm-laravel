<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\DataOperations\Models\DataOperation;

final class CreateDataOperation
{
    /** @param array<string, mixed> $attributes @param array<int, array<string, mixed>> $mappings */
    public function execute(int $teamId, ?int $actorId, array $attributes, array $mappings = []): DataOperation
    {
        return DB::transaction(function () use ($teamId, $actorId, $attributes, $mappings): DataOperation {
            $operation = DataOperation::query()->create(array_merge($attributes, ['team_id' => $teamId, 'actor_id' => $actorId, 'status' => 'draft']));
            foreach ($mappings as $mapping) {
                $operation->mappings()->create($mapping);
            }

            return $operation->load('mappings');
        });
    }
}
