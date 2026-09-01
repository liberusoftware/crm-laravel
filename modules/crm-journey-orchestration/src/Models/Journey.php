<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestration\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $status
 * @property int $version
 */
final class Journey extends Model
{
    use IsTenantModel;

    protected $table = 'crm_journeys';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['version' => 'integer', 'definition' => 'array', 'controls' => 'array'];
    }

    public function runs(): HasMany
    {
        return $this->hasMany(JourneyRun::class, 'journey_id');
    }
}
