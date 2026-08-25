<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearch\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $user_id @property bool $shared */
final class SearchView extends Model
{
    protected $table = 'crm_search_views';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['filters' => 'array', 'shared' => 'boolean'];
    }
}
