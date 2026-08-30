<?php

declare(strict_types=1);

namespace Liberu\CRM\Knowledge\Models;

use Illuminate\Database\Eloquent\Model;

final class KnowledgeVersion extends Model
{
    protected $table = 'crm_knowledge_versions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
