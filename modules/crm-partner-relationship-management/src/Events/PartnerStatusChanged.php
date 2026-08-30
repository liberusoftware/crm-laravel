<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Events;

use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerAccount;

final readonly class PartnerStatusChanged
{
    public function __construct(public PartnerAccount $partner, public string $status) {}
}
