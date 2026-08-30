<?php

declare(strict_types=1);

namespace Liberu\CRM\Communities\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $space_id @property string $subject_key @property string $status @property int $points */
final class CommunityMembership extends Model
{
    protected $table = 'crm_community_memberships';

    protected $guarded = [];
}
