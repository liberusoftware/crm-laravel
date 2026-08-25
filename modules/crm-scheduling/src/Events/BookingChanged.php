<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\CRM\Scheduling\Models\Booking;

final class BookingChanged
{
    use Dispatchable;

    public function __construct(public readonly Booking $booking, public readonly string $operation) {}
}
