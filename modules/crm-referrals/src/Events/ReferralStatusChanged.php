<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Events;

use Liberu\CRM\Referrals\Models\Referral;

final readonly class ReferralStatusChanged
{
    public function __construct(public Referral $referral, public string $status) {}
}
