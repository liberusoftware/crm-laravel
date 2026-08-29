<?php

declare(strict_types=1);

namespace Liberu\CRM\Knowledge\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property string $status @property string $visibility */
final class KnowledgeArticle extends Model
{
    protected $table = 'crm_knowledge_articles';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime', 'stale_at' => 'datetime', 'metadata' => 'array'];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(KnowledgeVersion::class, 'article_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(KnowledgeEvent::class, 'article_id');
    }
}
