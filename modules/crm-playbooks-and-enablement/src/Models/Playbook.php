<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Models;

use Illuminate\Database\Eloquent\Model;

final class Playbook extends Model
{
    protected $table = 'crm_playbooks';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['steps' => 'array', 'active' => 'boolean'];
    }
}
