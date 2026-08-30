<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string $status
 * @property string|null $comment
 */
final class Approval extends Model
{
    protected $table = 'crm_work_approvals';

    protected $fillable = ['work_item_id', 'team_id', 'requested_by', 'reviewed_by', 'status', 'comment', 'reviewed_at'];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }
}
