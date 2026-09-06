<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $response_id @property string $status */
final class FeedbackCase extends Model
{
    use IsTenantModel;

    protected $table = 'crm_feedback_cases';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['closed_at' => 'datetime'];
    }
}
