<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Models;

use Illuminate\Database\Eloquent\Model;

final class WorkspaceUpdate extends Model
{
    protected $table = 'crm_sales_workspace_updates';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
