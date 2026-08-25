<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class PartnerActivity extends Model
{
    protected $table = 'crm_partner_activities';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'float', 'payload' => 'array', 'occurred_at' => 'datetime'];
    }
}
