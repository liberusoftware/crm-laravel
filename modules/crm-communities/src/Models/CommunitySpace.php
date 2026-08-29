<?php

declare(strict_types=1);

namespace Liberu\CRM\Communities\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $owner_id
 * @property string $name
 * @property string $status
 */
final class CommunitySpace extends Model
{
    protected $table = 'crm_community_spaces';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['settings' => 'array'];
    }
}
