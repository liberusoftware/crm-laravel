<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Models;

use Illuminate\Database\Eloquent\Model;

final class ResourceSkill extends Model
{
    protected $table = 'crm_resource_skills';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['proficiency' => 'integer', 'metadata' => 'array'];
    }
}
