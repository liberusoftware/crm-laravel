<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspace\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $sku @property bool $eligible */
final class WorkspaceProduct extends Model
{
    protected $table = 'crm_product_workspace_products';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'eligible' => 'boolean', 'price_book' => 'array', 'metadata' => 'array'];
    }
}
