<?php

declare(strict_types=1);

namespace Liberu\CRM\Attribution\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $visitor_key @property string $source @property float $cost */
final class Touchpoint extends Model
{
    use IsTenantModel;

    protected $table = 'crm_attribution_touchpoints';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['cost' => 'decimal:2', 'metadata' => 'array', 'occurred_at' => 'datetime'];
    }
}
