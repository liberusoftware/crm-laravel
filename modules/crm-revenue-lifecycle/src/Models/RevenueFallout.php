<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Models;

use Illuminate\Database\Eloquent\Model;

final class RevenueFallout extends Model
{
    protected $table = 'crm_revenue_fallout';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
