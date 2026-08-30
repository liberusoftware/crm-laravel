<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Events;

use Liberu\CRM\WebIntent\Models\WebIntentEngagement;

final readonly class EngagementRecorded
{
    public function __construct(public WebIntentEngagement $engagement) {}
}
