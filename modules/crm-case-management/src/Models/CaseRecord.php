<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagement\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int|null $owner_id
 * @property int|null $parent_id
 * @property string $case_key
 * @property string $status
 * @property string $priority
 * @property int $escalation_level
 */
final class CaseRecord extends Model
{
    use IsTenantModel;

    protected $table = 'crm_cases';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['related_refs' => 'array', 'entitlement' => 'array', 'escalation_level' => 'integer'];
    }
}
