<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class PartnerContact extends Model
{
    protected $table = 'crm_partner_contacts';

    protected $guarded = [];
}
