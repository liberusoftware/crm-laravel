<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Events;

use Liberu\CRM\Prospecting\Models\Prospect;

final readonly class ProspectRevealed
{
    public function __construct(public Prospect $prospect) {}
}
