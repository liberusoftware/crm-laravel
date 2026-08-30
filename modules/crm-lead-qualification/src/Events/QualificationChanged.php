<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\CRM\LeadQualification\Models\LeadQualification;

final class QualificationChanged implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly LeadQualification $qualification, public readonly string $change) {}
}
