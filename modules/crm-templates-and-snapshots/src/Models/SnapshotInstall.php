<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Models;

use Illuminate\Database\Eloquent\Model;

final class SnapshotInstall extends Model
{
    protected $table = 'crm_snapshot_installs';

    protected $fillable = ['team_id', 'bundle_id', 'version', 'status', 'installed_by'];

    protected function casts(): array
    {
        return ['version' => 'integer'];
    }
}
