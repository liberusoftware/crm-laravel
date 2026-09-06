<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnels\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property string $status */
final class Funnel extends Model
{
    use IsTenantModel;

    protected $table = 'crm_funnels';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function pages(): HasMany
    {
        return $this->hasMany(FunnelPage::class, 'funnel_id');
    }
}
