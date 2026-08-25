<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Events;

use Liberu\CRM\Personalization\Models\PersonalizationDecision;

final readonly class PersonalizationDecided
{
    public function __construct(public PersonalizationDecision $decision) {}
}
