<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversation\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $status
 * @property array<int,array<string,mixed>>|null $transcript
 * @property array<string,mixed>|null $qualification
 * @property array<string,mixed>|null $booking
 * @property string|null $summary
 * @property string|null $confidence
 */
final class ReceptionConversation extends Model
{
    use IsTenantModel;

    protected $table = 'crm_ai_reception_conversations';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['transcript' => 'array', 'qualification' => 'array', 'booking' => 'array', 'confidence' => 'decimal:4'];
    }

    public function audits(): HasMany
    {
        return $this->hasMany(ReceptionAudit::class, 'conversation_id');
    }
}
