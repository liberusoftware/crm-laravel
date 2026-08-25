<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\CRM\ServiceAgent\Models\AgentCase;

final class AgentCaseUpdated
{
    use Dispatchable;

    public function __construct(public readonly AgentCase $case, public readonly string $operation) {}
}
