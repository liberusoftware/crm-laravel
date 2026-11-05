<?php

declare(strict_types=1);

namespace App\Filament\Forms;

use App\Enums\Role;
use App\Support\RolePermissionCatalog;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Spatie\Permission\Models\Role as SpatieRole;

final class RolePermissionBuilder
{
    public static function components(bool $platform = false): array
    {
        return [
            Section::make($platform ? 'Platform role' : 'Team role')
                ->description($platform ? 'Global role definitions affect every team that uses them. Only super administrators can make changes here.' : 'This role belongs only to the current team. Platform administration and security settings cannot be delegated here.')
                ->columnSpanFull()->columns(2)->schema([
                    TextInput::make('name')->required()->maxLength(255)
                        ->readOnly(fn (?SpatieRole $record): bool => $platform && $record !== null && in_array($record->name, Role::values(), true)),
                    Select::make('preset')->label('Start from a preset')->options(RolePermissionCatalog::presets())
                        ->placeholder('Build your own role')->live()->dehydrated(false)
                        ->helperText('Choosing a preset replaces the selection below. Presets exclude deletion; review and adjust before saving.')
                        ->afterStateUpdated(function (?string $state, Set $set): void {
                            if ($state !== null) {
                                $set('permissions', RolePermissionCatalog::preset($state));
                            }
                        }),
                ]),
            Section::make('Permission builder')->description('Search by feature, then choose View, Create, Edit or Delete. View access should accompany editing access.')
                ->columnSpanFull()->schema([
                    Placeholder::make('permission_summary')->label('Selection')
                        ->content(fn (Get $get): string => count($get('permissions') ?? []).' permissions selected; '.count(array_filter($get('permissions') ?? [], fn (string $name): bool => str_starts_with($name, 'delete_'))).' allow deletion.'),
                    CheckboxList::make('permissions')->label('Allowed actions')->options(RolePermissionCatalog::options($platform))
                        ->columns(['default' => 1, 'lg' => 2])->searchable()->bulkToggleable()->live()
                        ->helperText('Unchecked actions are denied. Changes apply to every member assigned this role.'),
                ]),
        ];
    }
}
