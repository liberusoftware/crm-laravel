<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class ReleaseDeployment extends Model
{
    protected $table = 'crm_release_deployments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['comparison' => 'array'];
    }
}
