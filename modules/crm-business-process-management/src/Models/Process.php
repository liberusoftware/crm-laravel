<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagement\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $key
 * @property string $name
 * @property string $status
 * @property int $version
 * @property array<string,mixed> $definition
 */
final class Process extends Model
{
    use IsTenantModel;

    protected $table = 'crm_bpm_processes';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['definition' => 'array', 'version' => 'integer'];
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ProcessRun::class, 'process_id');
    }
}
