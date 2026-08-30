<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Models;

use Illuminate\Database\Eloquent\Model;

final class SnapshotAudit extends Model
{
    protected $table = 'crm_snapshot_audits';

    protected $fillable = ['team_id', 'actor_id', 'event', 'details'];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
