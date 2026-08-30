<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string $metric
 * @property string $status
 */
final class FeedbackSurvey extends Model
{
    protected $table = 'crm_feedback_surveys';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['questions' => 'array', 'sampling' => 'array', 'delivery' => 'array'];
    }
}
