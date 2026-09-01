<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspace\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property int|null $parent_id @property string $account_type @property string $status @property array<string,mixed>|null $branding @property array<string,mixed>|null $usage_snapshot */
final class AgencyAccount extends Model
{
    use IsTenantModel;

    protected $table = 'crm_agency_accounts';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['branding' => 'array', 'usage_snapshot' => 'array'];
    }

    public function access(): HasMany
    {
        return $this->hasMany(AgencyAccess::class, 'account_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(AgencyAudit::class, 'account_id');
    }
}
