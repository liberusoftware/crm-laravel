<?php

declare(strict_types=1);

namespace Liberu\CRM\Knowledge\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $kind
 * @property string $status
 */
final class KnowledgeEvent extends Model
{
    protected $table = 'crm_knowledge_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
