<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Activities\Models\ActivityGoal;

final class CreateActivityGoal
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, array $attributes): ActivityGoal
    {
        return DB::transaction(fn (): ActivityGoal => ActivityGoal::query()->create(array_merge($attributes, ['team_id' => $teamId, 'status' => 'active']))->refresh());
    }
}
