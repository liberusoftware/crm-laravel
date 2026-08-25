<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversionOptimization\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $owner_id
 * @property string $status
 * @property int $allocation
 * @property array<int, mixed> $variants
 * @property array<int, mixed> $goals
 */
final class ConversionExperiment extends Model
{
    protected $table = 'crm_conversion_experiments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['variants' => 'array', 'goals' => 'array', 'statistical_policy' => 'array', 'experience' => 'array'];
    }
}
