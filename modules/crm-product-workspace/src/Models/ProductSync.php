<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspace\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $status */
final class ProductSync extends Model
{
    use IsTenantModel;

    protected $table = 'crm_product_workspace_syncs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }
}
