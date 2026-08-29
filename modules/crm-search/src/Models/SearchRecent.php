<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearch\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $user_id */
final class SearchRecent extends Model
{
    protected $table = 'crm_search_recents';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['viewed_at' => 'datetime'];
    }
}
