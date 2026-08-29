<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Events;

use Liberu\CRM\PredictiveModels\Models\Prediction;

final readonly class PredictionRecorded
{
    public function __construct(public Prediction $prediction) {}
}
