<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\PartnerRelationshipManagement\Filament\Resources\PartnerResource\Pages\ListPartners;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerAccount;

final class PartnerResource extends Resource
{
    protected static ?string $model = PartnerAccount::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), Select::make('tier')->options(['registered' => 'Registered', 'select' => 'Select', 'strategic' => 'Strategic'])->required(), Select::make('status')->options(['prospect' => 'Prospect', 'onboarding' => 'Onboarding', 'active' => 'Active', 'suspended' => 'Suspended', 'inactive' => 'Inactive'])]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('tier')->badge(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListPartners::route('/')];
    }
}
