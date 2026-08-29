<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversionOptimization\Actions;

use Liberu\CRM\ConversionOptimization\Models\ConversionExperiment;
use Liberu\CRM\ConversionOptimization\Models\ConversionObservation;

final class RecordConversion
{
    public function execute(int $teamId, ConversionExperiment $experiment, string $subjectKey, string $variant, string $event, float $value = 1): ConversionObservation
    {
        abort_unless((int) $experiment->team_id === $teamId && $experiment->status === 'active' && in_array($variant, $experiment->variants, true) && $subjectKey !== '' && $event !== '', 422);

        return ConversionObservation::query()->firstOrCreate(['team_id' => $teamId, 'experiment_id' => $experiment->id, 'subject_key' => $subjectKey, 'event' => $event], ['variant' => $variant, 'value' => $value]);
    }
}
