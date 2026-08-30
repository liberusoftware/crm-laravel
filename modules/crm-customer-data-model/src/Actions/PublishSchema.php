<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;
use Liberu\CRM\CustomerDataModel\Models\SchemaVersion;

final class PublishSchema
{
    public function execute(ObjectDefinition $object, ?int $actorId = null): SchemaVersion
    {
        return DB::transaction(function () use ($object, $actorId): SchemaVersion {
            $version = ((int) $object->current_version) + 1;
            $snapshot = [
                'object' => $object->only(['key', 'label', 'description']),
                'fields' => $object->fields()->orderBy('position')->get()->map(fn ($field): array => $field->only(['key', 'label', 'type', 'config', 'is_required', 'is_calculated', 'calculation', 'required_stages', 'position']))->all(),
                'layouts' => $object->layouts()->get()->map(fn ($layout): array => $layout->only(['key', 'label', 'sections', 'is_default']))->all(),
            ];
            $published = $object->versions()->create(['version' => $version, 'status' => 'published', 'snapshot' => $snapshot, 'published_by' => $actorId ?? auth()->id(), 'published_at' => now()]);
            $object->update(['status' => 'published', 'current_version' => $version]);

            return $published;
        });
    }
}
