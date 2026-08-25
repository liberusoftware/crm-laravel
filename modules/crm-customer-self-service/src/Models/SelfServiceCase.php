<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $profile_id @property string $status */
final class SelfServiceCase extends Model
{
    protected $table = 'crm_self_service_cases';

    protected $guarded = [];
}
