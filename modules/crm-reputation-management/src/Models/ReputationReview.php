<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class ReputationReview extends Model
{
    use IsTenantModel;

    protected $table = 'crm_reputation_reviews';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['rating' => 'integer', 'reviewed_at' => 'datetime'];
    }
}
