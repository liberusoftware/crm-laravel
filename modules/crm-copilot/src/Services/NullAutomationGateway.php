<?php

declare(strict_types=1);

namespace Liberu\CRM\Copilot\Services;

use Liberu\CRM\Copilot\Contracts\AutomationGateway;

final class NullAutomationGateway implements AutomationGateway
{
    public function complete(string $instruction, array $context): array
    {
        return ['status' => 'awaiting_provider', 'instruction' => $instruction, 'context_keys' => array_keys($context)];
    }
}
