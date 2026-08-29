<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Models;

use Illuminate\Database\Eloquent\Model;

final class PersonalizationOutcome extends Model
{
    protected $table = 'crm_personalization_outcomes';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'occurred_at' => 'datetime'];
    }
}
