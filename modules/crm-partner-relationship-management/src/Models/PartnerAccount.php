<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class PartnerAccount extends Model
{
    protected $table = 'crm_partner_accounts';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['competencies' => 'array', 'metadata' => 'array'];
    }
}
