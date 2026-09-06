<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $status
 * @property array<string,mixed>|null $profile
 */
final class Affiliate extends Model
{
    use IsTenantModel;

    protected $table = 'crm_affiliates';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['profile' => 'array'];
    }

    public function links(): HasMany
    {
        return $this->hasMany(AffiliateLink::class, 'affiliate_id');
    }
}
