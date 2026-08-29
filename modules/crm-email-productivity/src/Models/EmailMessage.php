<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int|null $mailbox_id
 * @property string $status
 */
final class EmailMessage extends Model
{
    protected $table = 'crm_email_messages';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['tracking' => 'array', 'scheduled_at' => 'datetime', 'sent_at' => 'datetime'];
    }
}
