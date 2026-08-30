<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Models;

use Illuminate\Database\Eloquent\Model;

final class BehaviorEvent extends Model
{
    protected $table = 'crm_segmentation_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['properties' => 'array', 'occurred_at' => 'datetime'];
    }
}
