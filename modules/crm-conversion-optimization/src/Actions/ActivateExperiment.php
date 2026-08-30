<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversionOptimization\Actions;

use Liberu\CRM\ConversionOptimization\Models\ConversionExperiment;

final class ActivateExperiment
{
    public function execute(int $teamId, int $userId, ConversionExperiment $experiment): ConversionExperiment
    {
        abort_unless((int) $experiment->team_id === $teamId && (int) $experiment->owner_id === $userId && $experiment->status === 'draft', 403);
        $experiment->update(['status' => 'active']);

        return $experiment->refresh();
    }
}
