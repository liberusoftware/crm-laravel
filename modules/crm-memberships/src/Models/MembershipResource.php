<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships\Models;

use Illuminate\Database\Eloquent\Model;

final class MembershipResource extends Model
{
    protected $table = 'crm_membership_resources';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
