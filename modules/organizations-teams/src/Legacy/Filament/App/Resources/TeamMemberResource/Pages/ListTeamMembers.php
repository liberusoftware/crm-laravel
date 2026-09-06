<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TeamMemberResource\Pages;

use App\Enums\Role;
use App\Filament\App\Resources\TeamMemberResource;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamManagementService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Validation\Rules\Password;

class ListTeamMembers extends ListRecords
{
    protected static string $resource = TeamMemberResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('addMember')
                ->label('Add member')
                ->icon('heroicon-o-user-plus')
                ->schema([
                    TextInput::make('name')->maxLength(255),
                    TextInput::make('email')->email()->required(),
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->rule(Password::min(12)->mixedCase()->numbers()->symbols()),
                    Select::make('role')
                        ->options([
                            Role::Manager->value => 'Manager',
                            Role::SalesRep->value => 'Sales rep',
                            Role::Free->value => 'Free',
                        ])
                        ->required(),
                ])
                ->action(function (array $data, TeamManagementService $service): void {
                    $tenant = Filament::getTenant();
                    if (! $tenant instanceof Team) {
                        Notification::make()->title('No active team selected')->danger()->send();

                        return;
                    }

                    $existing = User::where('email', (string) $data['email'])->first();
                    if ($existing instanceof User && blank($data['password'] ?? null)) {
                        $service->addTeamMember($existing, $tenant, Role::from((string) $data['role']));
                        Notification::make()->title('Member added')->success()->send();

                        return;
                    }

                    if (! $existing instanceof User && blank($data['password'] ?? null)) {
                        Notification::make()->title('A password is required for a new account')->danger()->send();

                        return;
                    }

                    $service->createTeamMember($tenant, (string) ($data['name'] ?? $data['email']), (string) $data['email'], (string) $data['password'], Role::from((string) $data['role']));
                    Notification::make()->title('Member added')->success()->send();
                }),
        ];
    }
}
