<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class ReputationTemplate extends Model
{
    protected $table = 'crm_reputation_templates';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
