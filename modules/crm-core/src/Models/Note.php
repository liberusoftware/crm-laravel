<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Note extends Model
{
    protected $table = 'crm_core_notes';

    protected $fillable = ['team_id', 'author_id', 'body'];

    public function recordable(): MorphTo
    {
        return $this->morphTo();
    }
}
