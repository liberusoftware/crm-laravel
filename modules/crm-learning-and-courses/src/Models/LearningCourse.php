<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCourses\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $status */
final class LearningCourse extends Model
{
    protected $table = 'crm_learning_courses';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
