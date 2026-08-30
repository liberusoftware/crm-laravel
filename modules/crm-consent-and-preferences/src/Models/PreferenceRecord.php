<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Models;

use Illuminate\Database\Eloquent\Model;

/** @property string $state */
final class PreferenceRecord extends Model
{
    protected $table = 'crm_preference_records';

    protected $fillable = ['team_id', 'subject_type', 'subject_id', 'channel', 'topic', 'state', 'quiet_hours', 'timezone', 'actor_id'];

    protected function casts(): array
    {
        return ['quiet_hours' => 'array'];
    }
}
