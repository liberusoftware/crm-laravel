<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementFilament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\AffiliateManagement\Actions\ApproveAffiliate;
use Liberu\CRM\AffiliateManagement\Models\Affiliate;
use Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource\Pages\CreateAffiliate;
use Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource\Pages\EditAffiliate;
use Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource\Pages\ListAffiliates;

final class AffiliateResource extends Resource
{
    protected static ?string $model = Affiliate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-share';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(180),
            TextInput::make('email')->email()->maxLength(255),
            Select::make('status')->options(['applicant' => 'Applicant', 'active' => 'Active', 'suspended' => 'Suspended'])->required(),
            TextInput::make('payout_method')->maxLength(120),
            KeyValue::make('profile')->json(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('links_count')->counts('links')->label('Links'),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->recordActions([
            Action::make('approve')->label('Approve')->visible(fn (Affiliate $record): bool => $record->status === 'applicant')->action(function (Affiliate $record): void {
                $teamId = auth()->user()?->getAttribute('current_team_id');
                abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);
                app(ApproveAffiliate::class)->execute((int) $teamId, $record);
            }),
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
        return ['index' => ListAffiliates::route('/'), 'create' => CreateAffiliate::route('/create'), 'edit' => EditAffiliate::route('/{record}/edit')];
    }
}
