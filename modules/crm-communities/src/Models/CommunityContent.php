<?php

declare(strict_types=1);

namespace Liberu\CRM\Communities\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $space_id @property string $author_key @property string $kind @property string $status */
final class CommunityContent extends Model
{
    protected $table = 'crm_community_content';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
