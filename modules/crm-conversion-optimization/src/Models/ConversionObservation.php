<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversionOptimization\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $experiment_id @property string $subject_key @property string $variant @property string $event */
final class ConversionObservation extends Model
{
    protected $table = 'crm_conversion_observations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'float', 'context' => 'array'];
    }
}
