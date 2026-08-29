<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class Project extends Model
{
    protected $table = 'crm_projects';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['client_visible' => 'boolean', 'starts_at' => 'date', 'ends_at' => 'date'];
    }
}
