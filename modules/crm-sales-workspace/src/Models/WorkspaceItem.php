<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $status
 * @property string $priority
 * @property Carbon|null $due_at
 * @property Carbon|null $last_activity_at
 * @property string|null $next_action
 */
final class WorkspaceItem extends Model
{
    protected $table = 'crm_sales_workspace_items';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'last_activity_at' => 'datetime', 'risk_indicators' => 'array', 'customer_history' => 'array', 'metadata' => 'array'];
    }
}
