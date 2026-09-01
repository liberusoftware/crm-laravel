<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $profile_id @property string $field */
final class EnrichmentField extends Model
{
    use IsTenantModel;

    protected $table = 'crm_enrichment_fields';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['observed_at' => 'datetime'];
    }
}
