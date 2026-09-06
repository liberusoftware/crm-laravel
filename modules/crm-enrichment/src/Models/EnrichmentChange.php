<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $profile_id @property string $status */
final class EnrichmentChange extends Model
{
    use IsTenantModel;

    protected $table = 'crm_enrichment_changes';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['detected_at' => 'datetime'];
    }
}
