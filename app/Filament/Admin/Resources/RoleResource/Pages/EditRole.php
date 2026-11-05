<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use App\Models\User;
use App\Services\RoleConfigurationService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role as SpatieRole;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $role = $this->getRecord();
        abort_unless($role instanceof SpatieRole, 404);
        $data['permissions'] = $role->getPermissionNames()->all();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $actor = auth()->user();
        abort_unless($actor instanceof User && $record instanceof SpatieRole, 403);

        try {
            return app(RoleConfigurationService::class)->save($actor, null, $data, $record);
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages(collect($exception->errors())->mapWithKeys(fn (array $messages, string $field): array => ['data.'.$field => $messages])->all());
        }
    }
}
