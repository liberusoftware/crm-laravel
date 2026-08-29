<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Models;

use Illuminate\Database\Eloquent\Model;

final class ProjectRisk extends Model
{
    protected $table = 'crm_project_risks';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['mitigation' => 'array'];
    }
}
