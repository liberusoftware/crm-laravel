<?php

declare(strict_types=1);

namespace Liberu\CRM\MembershipsFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Memberships\Models\MembershipPlan;
use Liberu\CRM\MembershipsFilament\Resources\Pages\CreateMembershipPlan;
use Liberu\CRM\MembershipsFilament\Resources\Pages\EditMembershipPlan;
use Liberu\CRM\MembershipsFilament\Resources\Pages\ListMembershipPlans;

final class MembershipPlanResource extends Resource
{
    protected static ?string $model = MembershipPlan::class;

    protected static ?string $navigationLabel = 'Memberships';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListMembershipPlans::route('/'), 'create' => CreateMembershipPlan::route('/create'), 'edit' => EditMembershipPlan::route('/{record}/edit')];
    }
}
