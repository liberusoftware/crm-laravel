<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Models;

use Illuminate\Database\Eloquent\Model;

final class ProjectTask extends Model
{
    protected $table = 'crm_project_tasks';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['due_at' => 'date'];
    }
}
