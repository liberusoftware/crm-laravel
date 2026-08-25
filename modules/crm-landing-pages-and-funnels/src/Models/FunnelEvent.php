<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnels\Models;

use Illuminate\Database\Eloquent\Model;

final class FunnelEvent extends Model
{
    protected $table = 'crm_funnel_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
