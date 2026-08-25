<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Events;

use Liberu\CRM\QuotasAndIncentives\Models\CommissionCredit;

final readonly class CommissionCredited
{
    public function __construct(public CommissionCredit $credit) {}
}
