<?php

namespace Liberu\Analytics\Contracts;

use DateTimeImmutable;

final readonly class AnalyticsEvent
{
    /** @param array<string, mixed> $properties */
    public function __construct(
        public string $id,
        public string $name,
        public string $sequence,
        public DateTimeImmutable $occurredAt,
        public string $source,
        public ?string $anonymousId,
        public ?string $userId,
        public ?string $sessionId,
        public ?string $locale,
        public ?string $currency,
        public string $consentCategory,
        public array $properties = [],
    ) {}
}
