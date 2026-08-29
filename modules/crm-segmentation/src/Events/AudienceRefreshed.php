<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Liberu\CRM\Segmentation\Models\Audience;

final class AudienceRefreshed
{
    use Dispatchable;

    public function __construct(public readonly Audience $audience, public readonly int $memberCount) {}
}
