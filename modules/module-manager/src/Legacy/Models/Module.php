<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property bool $is_enabled
 */
class Module extends Model
{
    protected $fillable = ['name', 'is_enabled', 'installed_at'];

    protected $casts = [
        'is_enabled' => 'boolean',
        'installed_at' => 'datetime',
    ];
}
