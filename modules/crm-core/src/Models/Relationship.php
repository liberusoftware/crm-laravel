<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Relationship extends Model
{
    protected $table = 'crm_core_relationships';

    protected $fillable = ['team_id', 'relationship_type', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function from(): MorphTo
    {
        return $this->morphTo();
    }

    public function to(): MorphTo
    {
        return $this->morphTo();
    }
}
