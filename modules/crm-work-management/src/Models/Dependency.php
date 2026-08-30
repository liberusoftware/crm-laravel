<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $work_item_id @property int $depends_on_id */
final class Dependency extends Model
{
    protected $table = 'crm_work_dependencies';

    protected $fillable = ['work_item_id', 'depends_on_id', 'team_id'];
}
