<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveys\Models;

use Illuminate\Database\Eloquent\Model;

final class FormFollowUp extends Model
{
    protected $table = 'crm_forms_follow_ups';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime'];
    }
}
