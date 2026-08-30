<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;

final class CreateObject
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, array $attributes): ObjectDefinition
    {
        return DB::transaction(fn (): ObjectDefinition => ObjectDefinition::query()->create([
            'team_id' => $teamId,
            'key' => $attributes['key'],
            'label' => $attributes['label'],
            'description' => $attributes['description'] ?? null,
            'is_standard' => $attributes['is_standard'] ?? false,
            'status' => 'draft',
            'current_version' => 0,
        ]));
    }
}
