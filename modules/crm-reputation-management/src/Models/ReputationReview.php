<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class ReputationReview extends Model
{
    protected $table = 'crm_reputation_reviews';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['rating' => 'integer', 'reviewed_at' => 'datetime'];
    }
}
