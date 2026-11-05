<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TeamRoleResource\Pages;

use App\Filament\App\Resources\TeamRoleResource;
use App\Models\User;
use App\Services\RoleConfigurationService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role as SpatieRole;

class EditTeamRole extends EditRecord
{
    protected static string $resource = TeamRoleResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    #[\Override]
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var SpatieRole $role */
        $role = $this->record;
        $data['permissions'] = $role->getPermissionNames()->all();

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    #[\Override]
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = auth()->user();
        $team = TeamRoleResource::team();
        abort_unless($user instanceof User && $team !== null && $record instanceof SpatieRole, 403);

        try {
            return app(RoleConfigurationService::class)->save($user, $team, $data, $record);
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages(collect($exception->errors())->mapWithKeys(fn (array $messages, string $field): array => ['data.'.$field => $messages])->all());
        }
    }
}
