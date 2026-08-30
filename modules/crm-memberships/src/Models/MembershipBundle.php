<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships\Models;

use Illuminate\Database\Eloquent\Model;

final class MembershipBundle extends Model
{
    protected $table = 'crm_membership_bundles';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['plan_ids' => 'array', 'resource_ids' => 'array'];
    }
}
