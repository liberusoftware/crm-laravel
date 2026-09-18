<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $position
 * @property int $delay_minutes
 * @property string $channel
 * @property string|null $template
 * @property array|null $snippet
 */
final class EngagementStep extends Model
{
    protected $table = 'crm_engagement_steps';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['position' => 'integer', 'delay_minutes' => 'integer', 'snippet' => 'array'];
    }
}
