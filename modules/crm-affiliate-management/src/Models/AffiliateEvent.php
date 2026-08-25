<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class AffiliateEvent extends Model
{
    protected $table = 'crm_affiliate_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'decimal:2', 'commission' => 'decimal:2', 'metadata' => 'array'];
    }
}
