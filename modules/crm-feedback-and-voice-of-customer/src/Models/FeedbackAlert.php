<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $response_id @property string $status */
final class FeedbackAlert extends Model
{
    protected $table = 'crm_feedback_alerts';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }
}
