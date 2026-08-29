<?php

declare(strict_types=1);

namespace Liberu\CRM\Campaigns\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $owner_id
 * @property int|null $parent_id
 * @property string $name
 * @property string $status
 * @property float $budget
 * @property float $cost
 * @property float $revenue
 */
final class Campaign extends Model
{
    protected $table = 'crm_campaigns';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['objectives' => 'array', 'audience' => 'array', 'channels' => 'array', 'assets' => 'array', 'budget' => 'float', 'cost' => 'float', 'influence' => 'float', 'revenue' => 'float', 'starts_on' => 'date', 'ends_on' => 'date'];
    }
}
