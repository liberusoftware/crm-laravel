<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property int|null $owner_id
 * @property string $record_type
 * @property string $name
 * @property string $status
 * @property array<string, mixed>|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Record extends Model
{
    protected $table = 'crm_core_records';

    protected $fillable = ['record_type', 'team_id', 'owner_id', 'name', 'status', 'data', 'archived_at'];

    protected function casts(): array
    {
        return ['data' => 'array', 'archived_at' => 'datetime'];
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', 'crm_core_taggables');
    }

    /** @return MorphMany<Note, $this> */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'recordable');
    }

    public function timeline(): MorphMany
    {
        return $this->morphMany(TimelineEntry::class, 'recordable')->latest();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function archive(): void
    {
        $this->forceFill(['status' => 'archived', 'archived_at' => now()])->save();
    }
}
