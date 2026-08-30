<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Services;

final class WebIntentScorer
{
    /** @var array<string, int> */
    private const POINTS = ['page_view' => 1, 'content_view' => 3, 'pricing_view' => 8, 'demo_request' => 20, 'form_submit' => 15, 'download' => 10, 'video_complete' => 6];

    public function points(string $eventType, ?int $durationSeconds = null): int
    {
        $base = self::POINTS[$eventType] ?? 1;

        return min(100, $base + (int) floor(max(0, $durationSeconds ?? 0) / 60));
    }

    public function level(int $score): string
    {
        return match (true) {
            $score >= 50 => 'hot',
            $score >= 25 => 'warm',
            $score > 0 => 'cool',
            default => 'unknown',
        };
    }
}
