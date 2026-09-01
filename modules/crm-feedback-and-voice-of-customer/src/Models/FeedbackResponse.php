<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $survey_id @property int|null $score @property string|null $sentiment */
final class FeedbackResponse extends Model
{
    use IsTenantModel;

    protected $table = 'crm_feedback_responses';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['answers' => 'array'];
    }
}
