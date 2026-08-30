<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class ReleaseSnapshot extends Model
{
    protected $table = 'crm_release_snapshots';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['configuration' => 'array', 'test_data_policy' => 'array'];
    }
}
