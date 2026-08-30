<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Events;

use Liberu\CRM\WhiteLabel\Models\WhiteLabelSettings;

final readonly class WhiteLabelSettingsUpdated
{
    public function __construct(public WhiteLabelSettings $settings, public int $actorId) {}
}
