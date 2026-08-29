<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property bool $shared */
final class EmailTemplate extends Model
{
    protected $table = 'crm_email_templates';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['shared' => 'boolean'];
    }
}
