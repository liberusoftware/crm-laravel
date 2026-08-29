<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $member_id @property string $status */
final class MembershipGrant extends Model
{
    protected $table = 'crm_membership_grants';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'metadata' => 'array'];
    }
}
