<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property string $stage @property string $qualification @property int $fit_score @property int $engagement_score @property bool $nurture */
final class QualifiedLead extends Model
{
    use IsTenantModel;

    protected $table = 'crm_lead_qualification_leads';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['fit_score' => 'integer', 'engagement_score' => 'integer', 'nurture' => 'boolean', 'metadata' => 'array'];
    }

    public function events(): HasMany
    {
        return $this->hasMany(QualificationEvent::class, 'lead_id');
    }
}
