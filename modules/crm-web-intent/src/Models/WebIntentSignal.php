<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Models;

use Illuminate\Database\Eloquent\Model;

final class WebIntentSignal extends Model
{
    protected $table = 'crm_web_intent_signals';

    protected $fillable = ['team_id', 'visit_id', 'visitor_key', 'signal', 'points', 'metadata', 'occurred_at'];

    protected function casts(): array
    {
        return ['points' => 'integer', 'metadata' => 'array', 'occurred_at' => 'datetime'];
    }
}
