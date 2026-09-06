<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

final class CaptureReferral extends Model
{
    use IsTenantModel;

    protected $table = 'crm_lead_capture_referrals';

    protected $fillable = ['team_id', 'actor_id', 'code', 'referrer_type', 'referrer_id', 'referred_type', 'referred_id', 'status', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
