<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\TemplatesAndSnapshots\Actions\UpdateSnapshot as UpdateSnapshotAction;
use Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource;

final class EditSnapshot extends EditRecord
{
    protected static string $resource = SnapshotResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $payload = is_string($data['payload'] ?? null) ? json_decode($data['payload'], true, 512, JSON_THROW_ON_ERROR) : ($data['payload'] ?? []);

        return app(UpdateSnapshotAction::class)->execute((int) auth()->user()?->current_team_id, (int) auth()->id(), (int) $record->getKey(), ['name' => $data['name'], 'payload' => $payload, 'status' => $data['status'] ?? 'draft']);
    }
}
