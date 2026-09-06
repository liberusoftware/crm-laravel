<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class Playbook extends Model
{
    use IsTenantModel;

    protected $table = 'crm_playbooks';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['steps' => 'array', 'active' => 'boolean'];
    }
}
