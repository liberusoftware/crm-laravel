<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $survey_id
 * @property string $token
 */
final class FeedbackDelivery extends Model
{
    protected $table = 'crm_feedback_deliveries';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime', 'responded_at' => 'datetime'];
    }
}
