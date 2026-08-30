<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversation\Models;

use Illuminate\Database\Eloquent\Model;

final class ReceptionAudit extends Model
{
    protected $table = 'crm_ai_reception_audits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
