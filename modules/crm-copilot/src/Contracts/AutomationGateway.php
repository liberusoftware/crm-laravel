<?php

declare(strict_types=1);

namespace Liberu\CRM\Copilot\Contracts;

interface AutomationGateway
{
    /** @param array<string,mixed> $context @return array<string,mixed> */
    public function complete(string $instruction, array $context): array;
}
