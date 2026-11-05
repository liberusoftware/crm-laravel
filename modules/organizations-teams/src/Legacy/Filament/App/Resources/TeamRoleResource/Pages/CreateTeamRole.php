<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TeamRoleResource\Pages;

use App\Filament\App\Resources\TeamRoleResource;
use App\Models\User;
use App\Services\RoleConfigurationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateTeamRole extends CreateRecord
{
    protected static string $resource = TeamRoleResource::class;

    #[\Override]
    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        $team = TeamRoleResource::team();
        abort_unless($user instanceof User && $team !== null, 403);

        try {
            return app(RoleConfigurationService::class)->save($user, $team, $data);
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages(collect($exception->errors())->mapWithKeys(fn (array $messages, string $field): array => ['data.'.$field => $messages])->all());
        }
    }
}
