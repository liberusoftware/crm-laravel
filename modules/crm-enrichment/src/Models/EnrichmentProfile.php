<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string $entity_type
 * @property string $entity_key
 * @property int $confidence
 * @property array<string, mixed>|null $verification
 */
final class EnrichmentProfile extends Model
{
    protected $table = 'crm_enrichment_profiles';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['firmographic' => 'array', 'demographic' => 'array', 'technographic' => 'array', 'social' => 'array', 'verification' => 'array', 'enriched_at' => 'datetime'];
    }
}
