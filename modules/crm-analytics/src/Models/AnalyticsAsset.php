<?php

declare(strict_types=1);

namespace Liberu\CRM\Analytics\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $owner_id
 * @property string $kind
 * @property string $status
 * @property int $version
 * @property array<string, mixed> $definition
 * @property array<string, mixed>|null $lineage
 */
final class AnalyticsAsset extends Model
{
    use IsTenantModel;

    protected $table = 'crm_analytics_assets';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['definition' => 'array', 'lineage' => 'array'];
    }
}
