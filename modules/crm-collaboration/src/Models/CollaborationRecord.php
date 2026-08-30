<?php

declare(strict_types=1);

namespace Liberu\CRM\Collaboration\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $record_key @property string $kind @property string $status @property array<int,mixed>|null $mentions */
final class CollaborationRecord extends Model
{
    protected $table = 'crm_collaboration_records';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['mentions' => 'array'];
    }
}
