<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\CRM\LeadCapture\Models\LeadCapture;

final class CaptureFormSubmitted implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly LeadCapture $capture) {}
}
