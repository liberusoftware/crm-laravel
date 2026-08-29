<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\CRM\Telephony\Models\TelephonyCall;

final class CallLogged
{
    use Dispatchable;

    public function __construct(public readonly TelephonyCall $call) {}
}
