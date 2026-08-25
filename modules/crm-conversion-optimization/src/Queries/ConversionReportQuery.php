<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversionOptimization\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ConversionOptimization\Models\ConversionExperiment;
use Liberu\CRM\ConversionOptimization\Models\ConversionObservation;

final class ConversionReportQuery
{
    public function experiments(int $teamId): Builder
    {
        return ConversionExperiment::query()->where('team_id', $teamId)->latest();
    }

    public function report(int $teamId, ConversionExperiment $experiment): array
    {
        abort_unless($experiment->team_id === $teamId, 403);

        return ConversionObservation::query()->where('team_id', $teamId)->where('experiment_id', $experiment->id)->get()->groupBy('variant')->map(fn ($rows): array => ['observations' => $rows->count(), 'value' => $rows->sum('value')])->all();
    }
}
