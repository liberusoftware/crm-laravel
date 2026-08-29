<?php

declare(strict_types=1);

namespace Liberu\CRM\Documents\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $document_id @property string $event */
final class DocumentEvent extends Model
{
    protected $table = 'crm_document_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'occurred_at' => 'datetime'];
    }
}
