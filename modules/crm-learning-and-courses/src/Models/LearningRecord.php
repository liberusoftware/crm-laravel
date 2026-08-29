<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCourses\Models;

use Illuminate\Database\Eloquent\Model;

final class LearningRecord extends Model
{
    protected $table = 'crm_learning_records';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['score' => 'decimal:2', 'payload' => 'array'];
    }
}
