<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class Tag extends Model
{
    protected $table = 'crm_core_tags';

    protected $fillable = ['team_id', 'name', 'slug'];

    public function records(): MorphToMany
    {
        return $this->morphedByMany(Record::class, 'taggable', 'crm_core_taggables');
    }
}
