<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Models;

use Illuminate\Database\Eloquent\Model;

final class ProjectEvent extends Model
{
    protected $table = 'crm_project_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'occurred_at' => 'datetime'];
    }
}
