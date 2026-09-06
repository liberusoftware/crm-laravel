<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationIntelligence\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $owner_id @property string $status @property string|null $transcript @property array<string,mixed>|null $summary @property array<string,mixed>|null $insights */
final class Conversation extends Model
{
    use IsTenantModel;

    protected $table = 'crm_ci_conversations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['summary' => 'array', 'insights' => 'array', 'sentiment_policy' => 'array'];
    }
}
