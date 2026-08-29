<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource\Pages\CreateSubscription;
use Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource\Pages\EditSubscription;
use Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource\Pages\ListSubscriptions;
use Liberu\CRM\SaasPackaging\Models\SaasSubscription;

final class SubscriptionResource extends Resource
{
    protected static ?string $model = SaasSubscription::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([
            Select::make('plan_id')->relationship('plan', 'name')->required(),
            Select::make('status')->options(['trialing' => 'Trialing', 'active' => 'Active', 'suspended' => 'Suspended', 'cancelled' => 'Cancelled'])->required(),
            TextInput::make('billing_provider')->maxLength(100),
            TextInput::make('billing_reference')->maxLength(255),
        ]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('team_id'), TextColumn::make('status')->badge(), TextColumn::make('trial_ends_at')->dateTime(), TextColumn::make('cancelled_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListSubscriptions::route('/'), 'create' => CreateSubscription::route('/create'), 'edit' => EditSubscription::route('/{record}/edit')];
    }
}
