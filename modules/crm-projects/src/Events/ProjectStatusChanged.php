<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Events;

use Liberu\CRM\Projects\Models\Project;

final readonly class ProjectStatusChanged
{
    public function __construct(public Project $project, public string $status) {}
}
