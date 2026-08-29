<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Events;

use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookAssignment;

final readonly class PlaybookCompleted
{
    public function __construct(public PlaybookAssignment $assignment) {}
}
