<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Events;

use Liberu\CRM\WebIntent\Models\WebIntentVisit;

final readonly class VisitRecorded
{
    public function __construct(public WebIntentVisit $visit) {}
}
