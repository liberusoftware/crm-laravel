<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Events;

use Liberu\CRM\ProposalsAndQuotes\Models\Proposal;

final readonly class ProposalStatusChanged
{
    public function __construct(public Proposal $proposal, public string $status) {}
}
