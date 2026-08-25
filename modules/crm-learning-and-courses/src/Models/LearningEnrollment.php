<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCourses\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $member_id
 * @property float $progress
 * @property string $status
 */
final class LearningEnrollment extends Model
{
    protected $table = 'crm_learning_enrollments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['progress' => 'decimal:2', 'completed_at' => 'datetime'];
    }
}
