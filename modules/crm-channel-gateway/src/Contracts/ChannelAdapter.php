<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGateway\Contracts;

interface ChannelAdapter
{
    public function send(string $address, string $body, array $metadata = []): string;

    public function health(): bool;
}
