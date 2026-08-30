<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Favorite extends Model
{
    protected $table = 'crm_core_favorites';

    protected $fillable = ['team_id', 'user_id'];

    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }
}
