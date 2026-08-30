<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Services;

final class QualificationScorer
{
    /** @param array<string, mixed> $attributes */
    public function scores(array $attributes): array
    {
        $fit = max(0, min(100, (int) ($attributes['fit_score'] ?? 0)));
        $engagement = max(0, min(100, (int) ($attributes['engagement_score'] ?? 0)));

        return ['fit_score' => $fit, 'engagement_score' => $engagement, 'total_score' => (int) round(($fit + $engagement) / 2)];
    }
}
