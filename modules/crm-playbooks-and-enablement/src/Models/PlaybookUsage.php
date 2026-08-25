<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Models;

use Illuminate\Database\Eloquent\Model;

final class PlaybookUsage extends Model
{
    protected $table = 'crm_playbook_usage';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'occurred_at' => 'datetime'];
    }
}
