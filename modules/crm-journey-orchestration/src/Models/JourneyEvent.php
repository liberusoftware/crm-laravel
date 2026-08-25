<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestration\Models;

use Illuminate\Database\Eloquent\Model;

final class JourneyEvent extends Model
{
    protected $table = 'crm_journey_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
