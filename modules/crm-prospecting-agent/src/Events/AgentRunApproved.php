<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Events;

use Liberu\CRM\ProspectingAgent\Models\AgentRun;

final readonly class AgentRunApproved
{
    public function __construct(public AgentRun $run) {}
}
