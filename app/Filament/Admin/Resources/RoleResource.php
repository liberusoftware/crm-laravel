<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\Role;
use App\Filament\Admin\Resources\RoleResource\Pages\CreateRole;
use App\Filament\Admin\Resources\RoleResource\Pages\EditRole;
use App\Filament\Admin\Resources\RoleResource\Pages\ListRoles;
use App\Filament\Forms\RolePermissionBuilder;
use App\Models\User;
use App\Services\RoleConfigurationService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleResource extends Resource
{
    protected static ?string $model = SpatieRole::class;

    protected static ?string $slug = 'shield/roles';

    protected static ?string $navigationLabel = 'Global roles';

    protected static string|\UnitEnum|null $navigationGroup = 'Security & access';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static bool $isScopedToTenant = false;

    public static function canAccess(): bool
    {
        $actor = auth()->user();

        return $actor instanceof User && $actor->isSuperAdmin();
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
        return self::canAccess() && $record->getAttribute('team_id') === null && $record->getAttribute('guard_name') === 'web';
    }

    public static function canEdit(Model $record): bool
    {
        return self::canView($record) && mb_strtolower($record->getAttribute('name')) !== Role::SuperAdmin->value;
    }

    public static function canDelete(Model $record): bool
    {
        return self::canView($record) && ! in_array(mb_strtolower($record->getAttribute('name')), Role::values(), true);
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return SpatieRole::query()->whereNull('team_id')->where('guard_name', 'web');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(RolePermissionBuilder::components(platform: true));
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('name')->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('scope')->getStateUsing(fn (): string => 'Global')->badge()->color('warning'),
            TextColumn::make('permissions_count')->counts('permissions')->label('Permissions'),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make()->using(function (SpatieRole $record): bool {
                $actor = auth()->user();
                abort_unless($actor instanceof User, 403);
                app(RoleConfigurationService::class)->delete($actor, null, $record);

                return true;
            }),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListRoles::route('/'), 'create' => CreateRole::route('/create'), 'edit' => EditRole::route('/{record}/edit')];
    }
}
