<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class MarketingPlatformSettings extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $hidden = ['credentials'];

    protected function casts(): array
    {
        return ['credentials' => 'encrypted:array', 'options' => 'array', 'setup_completed_at' => 'datetime'];
    }
}
