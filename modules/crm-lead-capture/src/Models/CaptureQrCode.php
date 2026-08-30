<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Models;

use Illuminate\Database\Eloquent\Model;

final class CaptureQrCode extends Model
{
    protected $table = 'crm_lead_capture_qr_codes';

    protected $fillable = ['team_id', 'actor_id', 'name', 'code', 'destination', 'status', 'metadata', 'scan_count'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
