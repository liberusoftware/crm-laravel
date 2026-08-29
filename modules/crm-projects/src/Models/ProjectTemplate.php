<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Models;

use Illuminate\Database\Eloquent\Model;

final class ProjectTemplate extends Model
{
    protected $table = 'crm_project_templates';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['milestones' => 'array', 'tasks' => 'array', 'active' => 'boolean'];
    }
}
