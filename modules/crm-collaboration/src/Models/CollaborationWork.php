<?php

declare(strict_types=1);

namespace Liberu\CRM\Collaboration\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string $queue_key
 * @property string $subject_key
 * @property string|null $assignee_key
 * @property string $status
 */
final class CollaborationWork extends Model
{
    use IsTenantModel;

    protected $table = 'crm_collaboration_work';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
