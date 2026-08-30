<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Services;

use Liberu\CRM\LeadCapture\Models\LeadCapture;

final class CaptureDeduplicator
{
    /** @param array<string, mixed> $attributes */
    public function key(array $attributes): string
    {
        if (filled($attributes['dedupe_key'] ?? null)) {
            return (string) $attributes['dedupe_key'];
        }

        return hash('sha256', implode('|', [strtolower(trim((string) ($attributes['email'] ?? ''))), preg_replace('/\D+/', '', (string) ($attributes['phone'] ?? '')), strtolower(trim((string) ($attributes['source'] ?? '')))]));
    }

    public function find(int $teamId, string $key): ?LeadCapture
    {
        return LeadCapture::query()->where('team_id', $teamId)->where('dedupe_key', $key)->latest('id')->first();
    }
}
