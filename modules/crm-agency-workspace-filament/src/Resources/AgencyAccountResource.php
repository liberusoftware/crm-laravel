<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AgencyWorkspace\Models\AgencyAccount;
use Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource\Pages\CreateAgencyAccountPage;
use Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource\Pages\EditAgencyAccount;
use Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource\Pages\ListAgencyAccounts;

final class AgencyAccountResource extends Resource
{
    protected static ?string $model = AgencyAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(180),
            Select::make('account_type')->options(['agency' => 'Agency', 'client' => 'Client', 'sub_account' => 'Sub-account'])->required(),
            Select::make('status')->options(['active' => 'Active', 'suspended' => 'Suspended', 'archived' => 'Archived'])->required(),
            TextInput::make('parent_id')->numeric()->minValue(1),
            KeyValue::make('branding')->json(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('account_type')->badge(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('access_count')->counts('access')->label('Access grants'),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', (int) $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListAgencyAccounts::route('/'), 'create' => CreateAgencyAccountPage::route('/create'), 'edit' => EditAgencyAccount::route('/{record}/edit')];
    }
}
