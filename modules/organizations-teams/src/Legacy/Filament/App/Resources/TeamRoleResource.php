<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Enums\Role;
use App\Filament\App\Resources\TeamRoleResource\Pages\CreateTeamRole;
use App\Filament\App\Resources\TeamRoleResource\Pages\EditTeamRole;
use App\Filament\App\Resources\TeamRoleResource\Pages\ListTeamRoles;
use App\Filament\Forms\RolePermissionBuilder;
use App\Models\Team;
use App\Models\User;
use App\Services\RoleConfigurationService;
use App\Support\RolePermissionCatalog;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * A team admin manages their team's CUSTOM roles (F4). These are spatie roles
 * scoped to the current team via roles.team_id; the four system roles (team_id
 * null) are managed globally and never shown/edited here. Permissions that
 * manage roles/permissions/users are NOT grantable — a team admin can't mint a
 * role that escalates privilege.
 */
class TeamRoleResource extends Resource
{
    public static function permissionResource(): string
    {
        return 'team_role';
    }

    protected static ?string $model = SpatieRole::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Team';

    protected static ?string $navigationLabel = 'Custom roles';

    protected static ?string $slug = 'custom-roles';

    // Spatie Role has no `team` ownership relation, so opt out of Filament's
    // automatic tenant scoping and scope to roles.team_id manually below.
    protected static bool $isScopedToTenant = false;

    public static function team(): ?Team
    {
        $id = Filament::getTenant()?->getKey() ?? Auth::user()?->getAttribute('current_team_id');

        return $id === null ? null : Team::query()->find($id);
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        $team = self::team();

        return $user instanceof User && $team !== null && app(RoleConfigurationService::class)->canManageTeam($user, $team);
    }

    public static function canViewAny(): bool
    {
        return self::canAccess();
    }

    public static function canCreate(): bool
    {
        return self::canAccess();
    }

    public static function canView(Model $record): bool
    {
        return self::canAccess() && self::team() !== null && (int) $record->getAttribute('team_id') === (int) self::team()->getKey()
            && $record->getAttribute('guard_name') === 'web';
    }

    public static function canEdit(Model $record): bool
    {
        return self::canView($record);
    }

    public static function canDelete(Model $record): bool
    {
        return self::canView($record) && ! in_array(mb_strtolower($record->getAttribute('name')), Role::values(), true);
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function grantablePermissions(): array
    {
        return RolePermissionCatalog::options();
    }

    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        $team = self::team();

        return SpatieRole::query()->where('guard_name', 'web')->where('team_id', $team?->getKey())
            ->when($team === null, fn (Builder $query): Builder => $query->whereRaw('1 = 0'));
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components(RolePermissionBuilder::components());
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->emptyStateHeading('Build your first team role')
            ->emptyStateDescription('Start with a Sales, Support, Social media or Advertising preset, then adjust permissions.')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('permissions_count')->counts('permissions')->label('Permissions'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->using(function (SpatieRole $record): bool {
                    $user = Auth::user();
                    $team = self::team();
                    abort_unless($user instanceof User && $team !== null, 403);
                    app(RoleConfigurationService::class)->delete($user, $team, $record);

                    return true;
                }),
            ]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListTeamRoles::route('/'),
            'create' => CreateTeamRole::route('/create'),
            'edit' => EditTeamRole::route('/{record}/edit'),
        ];
    }
}
