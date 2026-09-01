<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboarding\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $owner_id
 * @property string $client_key
 * @property string $status
 * @property int $health
 * @property array<string, mixed>|null $intake
 */
final class ClientOnboarding extends Model
{
    use IsTenantModel;

    protected $table = 'crm_client_onboardings';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['intake' => 'array', 'connections' => 'array', 'snapshot' => 'array', 'target_launch_on' => 'date', 'health' => 'integer'];
    }
}
