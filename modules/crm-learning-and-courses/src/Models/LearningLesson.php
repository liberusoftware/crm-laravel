<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCourses\Models;

use Illuminate\Database\Eloquent\Model;

final class LearningLesson extends Model
{
    protected $table = 'crm_learning_lessons';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['drip' => 'array'];
    }
}
