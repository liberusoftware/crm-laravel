<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\CRM\SalesPipelines\Models\Opportunity;

final class OpportunityChanged
{
    use Dispatchable;

    public function __construct(public readonly Opportunity $opportunity, public readonly string $operation) {}
}
