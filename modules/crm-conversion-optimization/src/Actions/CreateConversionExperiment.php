<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversionOptimization\Actions;

use Liberu\CRM\ConversionOptimization\Models\ConversionExperiment;

final class CreateConversionExperiment
{
    /** @param array{name?:string,variants?:array<int,mixed>,goals?:array<int,mixed>,allocation?:int,statistical_policy?:array<string,mixed>,experience?:array<string,mixed>} $input */
    public function execute(int $teamId, int $userId, array $input): ConversionExperiment
    {
        $name = trim((string) ($input['name'] ?? ''));
        $variants = $input['variants'] ?? [];
        $allocation = (int) ($input['allocation'] ?? 100);
        abort_unless($name !== '' && count($variants) >= 2 && $allocation >= 1 && $allocation <= 100, 422);

        return ConversionExperiment::query()->create(['team_id' => $teamId, 'owner_id' => $userId, 'name' => $name, 'status' => 'draft', 'variants' => $variants, 'goals' => $input['goals'] ?? [], 'allocation' => $allocation, 'statistical_policy' => $input['statistical_policy'] ?? ['confidence' => 0.95], 'experience' => $input['experience'] ?? []]);
    }
}
