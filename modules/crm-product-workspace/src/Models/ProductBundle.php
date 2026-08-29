<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspace\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property array $product_ids */
final class ProductBundle extends Model
{
    protected $table = 'crm_product_workspace_bundles';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['product_ids' => 'array'];
    }
}
