<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $lifecycle_stage
 * @property string $qualification_status
 * @property int $version
 * @property int $total_score
 */
final class LeadQualification extends Model
{
    protected $table = 'crm_lead_qualifications';

    protected $fillable = ['team_id', 'actor_id', 'subject_type', 'subject_id', 'framework_id', 'lifecycle_stage', 'fit_score', 'engagement_score', 'total_score', 'qualification_status', 'disqualification_reason', 'nurture_until', 'converted_at', 'version', 'metadata'];

    protected function casts(): array
    {
        return ['fit_score' => 'integer', 'engagement_score' => 'integer', 'total_score' => 'integer', 'version' => 'integer', 'metadata' => 'array', 'nurture_until' => 'datetime', 'converted_at' => 'datetime'];
    }

    /** @return BelongsTo<QualificationFramework, $this> */
    public function framework(): BelongsTo
    {
        return $this->belongsTo(QualificationFramework::class, 'framework_id');
    }

    /** @return HasMany<StageHistory, $this> */
    public function stageHistory(): HasMany
    {
        return $this->hasMany(StageHistory::class, 'qualification_id');
    }

    /** @return HasMany<NurtureEnrollment, $this> */
    public function nurtures(): HasMany
    {
        return $this->hasMany(NurtureEnrollment::class, 'qualification_id');
    }
}
