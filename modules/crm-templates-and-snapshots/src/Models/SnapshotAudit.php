<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

final class SnapshotAudit extends Model
{
    use IsTenantModel;

    protected $table = 'crm_snapshot_audits';

    protected $fillable = ['team_id', 'actor_id', 'event', 'details'];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
