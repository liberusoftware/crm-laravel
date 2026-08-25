<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Models;

use Illuminate\Database\Eloquent\Model;

final class PlaybookAssignment extends Model
{
    protected $table = 'crm_playbook_assignments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['evidence' => 'array', 'checklist' => 'array', 'completed_at' => 'datetime'];
    }
}
