<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Models;

use Illuminate\Database\Eloquent\Model;

final class WebIntentConversion extends Model
{
    protected $table = 'crm_web_intent_conversions';

    protected $fillable = ['team_id', 'visitor_key', 'visit_id', 'target_type', 'target_id', 'actor_id', 'status', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
