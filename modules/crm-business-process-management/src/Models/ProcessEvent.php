<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class ProcessEvent extends Model
{
    protected $table = 'crm_bpm_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
