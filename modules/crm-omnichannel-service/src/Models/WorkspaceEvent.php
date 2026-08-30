<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService\Models;

use Illuminate\Database\Eloquent\Model;

/** @property string $kind @property string $status */
final class WorkspaceEvent extends Model
{
    protected $table = 'crm_omnichannel_workspace_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'payload' => 'array'];
    }
}
