<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\TemplatesAndSnapshots\Actions\CreateSnapshot as CreateSnapshotAction;
use Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource;

final class CreateSnapshot extends CreateRecord
{
    protected static string $resource = SnapshotResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $payload = is_string($data['payload'] ?? null) ? json_decode($data['payload'], true, 512, JSON_THROW_ON_ERROR) : ($data['payload'] ?? []);

        return app(CreateSnapshotAction::class)->execute((int) auth()->user()?->current_team_id, (int) auth()->id(), ['name' => $data['name'], 'payload' => $payload, 'status' => $data['status'] ?? 'draft']);
    }
}
