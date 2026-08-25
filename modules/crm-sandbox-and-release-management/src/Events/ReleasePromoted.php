<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseDeployment;

final class ReleasePromoted
{
    use Dispatchable;

    public function __construct(public readonly ReleaseDeployment $deployment) {}
}
