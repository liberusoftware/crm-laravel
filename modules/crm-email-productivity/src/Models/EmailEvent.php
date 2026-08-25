<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $message_id @property string $event */
final class EmailEvent extends Model
{
    protected $table = 'crm_email_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime', 'metadata' => 'array'];
    }
}
