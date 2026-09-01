<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspace\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $customer_id @property string $status */
final class ProductEntitlement extends Model
{
    use IsTenantModel;

    protected $table = 'crm_product_workspace_entitlements';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'metadata' => 'array'];
    }
}
