<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Models;

use Illuminate\Database\Eloquent\Model;

final class ProjectTime extends Model
{
    protected $table = 'crm_project_time';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['hours' => 'float', 'worked_at' => 'date'];
    }
}
