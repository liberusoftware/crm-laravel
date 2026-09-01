<?php

declare(strict_types=1);

namespace Liberu\CRM\Contracts\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $contract_id @property int $actor_id @property string $type @property string $status */
final class ContractEvent extends Model
{
    use IsTenantModel;

    protected $table = 'crm_contract_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
