<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQ\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $owner_id
 * @property string $status
 * @property array<int, mixed> $lines
 * @property float $subtotal
 * @property float $discount
 * @property float $total
 */
final class CpqQuote extends Model
{
    protected $table = 'crm_cpq_quotes';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['configuration' => 'array', 'lines' => 'array', 'subtotal' => 'float', 'discount' => 'float', 'total' => 'float', 'margin' => 'float'];
    }
}
