<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatform\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $profile_id */
final class CdpIdentity extends Model
{
    protected $table = 'crm_cdp_identities';

    protected $guarded = [];
}
