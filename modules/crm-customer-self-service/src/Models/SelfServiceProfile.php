<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $user_id
 * @property string $email
 */
final class SelfServiceProfile extends Model
{
    protected $table = 'crm_self_service_profiles';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['preferences' => 'array'];
    }
}
