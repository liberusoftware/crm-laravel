<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Core\Models\Favorite;
use Liberu\CRM\Core\Models\Record;
use Liberu\CRM\Core\Models\Relationship;

final class MergeRecords
{
    public function execute(Record $survivor, Record $duplicate, ?int $actorId = null): Record
    {
        if ((string) $survivor->getKey() === (string) $duplicate->getKey()) {
            throw ValidationException::withMessages(['record' => 'Choose two distinct records to merge.']);
        }

        return DB::transaction(function () use ($survivor, $duplicate, $actorId): Record {
            $records = Record::query()->whereKey([$survivor->getKey(), $duplicate->getKey()])->orderBy('id')->lockForUpdate()->get();
            $target = $records->find($survivor->getKey());
            $source = $records->find($duplicate->getKey());
            abort_unless($target instanceof Record && $source instanceof Record, 404);
            if ($target->team_id !== $source->team_id || $target->record_type !== $source->record_type) {
                throw ValidationException::withMessages(['record' => 'Merged records must have the same type and team.']);
            }
            $target->data = array_replace($source->data ?? [], $target->data ?? []);
            if ($target->owner_id === null) {
                $target->owner_id = $source->owner_id;
            }
            $target->save();
            foreach (['notes', 'timeline'] as $relation) {
                $source->$relation()->where('team_id', $target->team_id)->update([
                    'recordable_type' => $target->getMorphClass(), 'recordable_id' => $target->getKey(),
                ]);
            }
            $target->tags()->syncWithoutDetaching($source->tags()->where('crm_core_tags.team_id', $target->team_id)->pluck('crm_core_tags.id')->all());
            $source->tags()->detach();
            $this->moveFavorites($target, $source);
            $this->moveRelationships($target, $source);
            $sourceId = $source->getKey();
            $source->delete();
            $target->timeline()->create([
                'team_id' => $target->team_id, 'actor_id' => $actorId ?? auth()->id(),
                'event_type' => 'record.merged', 'summary' => 'Duplicate record merged',
                'payload' => ['merged_record_id' => $sourceId],
            ]);

            return $target->refresh();
        });
    }

    private function moveFavorites(Record $target, Record $source): void
    {
        $favorites = Favorite::query()->where('team_id', $target->team_id)
            ->where('favoritable_type', $source->getMorphClass())->where('favoritable_id', $source->getKey())->get();
        foreach ($favorites as $favorite) {
            $exists = Favorite::query()->where('team_id', $target->team_id)->where('user_id', $favorite->user_id)
                ->where('favoritable_type', $target->getMorphClass())->where('favoritable_id', $target->getKey())->exists();
            if ($exists) {
                $favorite->delete();
            } else {
                $favorite->forceFill(['favoritable_type' => $target->getMorphClass(), 'favoritable_id' => $target->getKey()])->save();
            }
        }
    }

    private function moveRelationships(Record $target, Record $source): void
    {
        $relationships = Relationship::query()->where('team_id', $target->team_id)->where(function ($query) use ($source): void {
            $query->where(fn ($from) => $from->where('from_type', $source->getMorphClass())->where('from_id', $source->getKey()))
                ->orWhere(fn ($to) => $to->where('to_type', $source->getMorphClass())->where('to_id', $source->getKey()));
        })->get();
        foreach ($relationships as $relationship) {
            $identity = $relationship->only(['from_type', 'from_id', 'to_type', 'to_id', 'relationship_type']);
            foreach (['from', 'to'] as $side) {
                if ($identity[$side.'_type'] === $source->getMorphClass() && (string) $identity[$side.'_id'] === (string) $source->getKey()) {
                    $identity[$side.'_type'] = $target->getMorphClass();
                    $identity[$side.'_id'] = $target->getKey();
                }
            }
            if ($identity['from_type'] === $identity['to_type'] && (string) $identity['from_id'] === (string) $identity['to_id']) {
                $relationship->delete();

                continue;
            }
            $existing = Relationship::query()->where('team_id', $target->team_id)->where($identity)->whereKeyNot($relationship->getKey())->first();
            if ($existing !== null) {
                $existing->update(['metadata' => array_replace($relationship->metadata ?? [], $existing->metadata ?? [])]);
                $relationship->delete();
            } else {
                $relationship->forceFill($identity)->save();
            }
        }
    }
}
