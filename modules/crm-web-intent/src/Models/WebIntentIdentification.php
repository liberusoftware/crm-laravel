<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Models;

use Illuminate\Database\Eloquent\Model;

final class WebIntentIdentification extends Model
{
    protected $table = 'crm_web_intent_identifications';

    protected $fillable = ['team_id', 'visitor_key', 'adapter', 'account_name', 'account_domain', 'confidence', 'status', 'metadata'];

    protected function casts(): array
    {
        return ['confidence' => 'integer', 'metadata' => 'array'];
    }
}
