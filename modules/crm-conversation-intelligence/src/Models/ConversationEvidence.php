<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $conversation_id @property string $kind @property string $label @property string $content */
final class ConversationEvidence extends Model
{
    protected $table = 'crm_ci_evidence';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
