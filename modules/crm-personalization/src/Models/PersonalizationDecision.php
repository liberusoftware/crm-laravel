<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Models;

use Illuminate\Database\Eloquent\Model;

final class PersonalizationDecision extends Model
{
    protected $table = 'crm_personalization_decisions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['attributes' => 'array', 'holdout' => 'boolean', 'decided_at' => 'datetime'];
    }
}
