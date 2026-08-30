<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Models;

use Illuminate\Database\Eloquent\Model;

final class PolicyEvaluation extends Model
{
    protected $table = 'crm_policy_evaluations';

    protected $fillable = ['team_id', 'subject_type', 'subject_id', 'channel', 'topic', 'allowed', 'reasons', 'evaluated_at'];

    protected function casts(): array
    {
        return ['allowed' => 'boolean', 'reasons' => 'array', 'evaluated_at' => 'datetime'];
    }
}
