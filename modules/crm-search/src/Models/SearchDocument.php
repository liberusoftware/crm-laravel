<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearch\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $record_type @property string $record_key */
final class SearchDocument extends Model
{
    protected $table = 'crm_search_documents';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['attributes' => 'array', 'indexed_at' => 'datetime'];
    }
}
