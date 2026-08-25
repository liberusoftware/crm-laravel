<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Events;

use Liberu\CRM\ResourcePlanning\Models\ResourceBooking;

final readonly class ResourceBookingChanged
{
    public function __construct(public ResourceBooking $booking, public string $action) {}
}
