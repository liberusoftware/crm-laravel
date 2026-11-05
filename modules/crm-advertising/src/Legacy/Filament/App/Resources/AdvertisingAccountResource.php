<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Pages\MarketingConnections;
use App\Filament\App\Resources\AdvertisingAccountResource\Pages\CreateAdvertisingAccount;
use App\Filament\App\Resources\AdvertisingAccountResource\Pages\EditAdvertisingAccount;
use App\Filament\App\Resources\AdvertisingAccountResource\Pages\ListAdvertisingAccounts;
use App\Filament\App\Resources\AdvertisingAccountResource\Pages\ViewAdvertisingAccount;
use App\Filament\App\Resources\AdvertisingAccountResource\RelationManagers\AdSetsRelationManager;
use App\Filament\App\Resources\AdvertisingAccountResource\RelationManagers\AdsRelationManager;
use App\Filament\App\Resources\AdvertisingAccountResource\RelationManagers\CampaignsRelationManager;
use App\Filament\Concerns\EnforcesResourcePermissions;
use App\Models\AdvertisingAccount;
use App\Services\Marketing\PlatformConfiguration;
use App\Services\Zernio\ZernioTenantService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdvertisingAccountResource extends Resource
{
    use EnforcesResourcePermissions;

    protected static ?string $model = AdvertisingAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Account Name')
                            ->helperText('Give this account a memorable name'),
                        Select::make('platform')->label('Advertising network')->options([
                            'facebook' => 'Meta — Facebook', 'instagram' => 'Meta — Instagram', 'google' => 'Google Ads',
                            'linkedin' => 'LinkedIn Ads', 'twitter' => 'X Ads', 'tiktok' => 'TikTok Ads',
                            'pinterest' => 'Pinterest Ads', 'reddit' => 'Reddit Ads', 'microsoft' => 'Microsoft Ads',
                        ])->required(),
                        TextInput::make('account_id')->label('Platform ad account ID')->required()->maxLength(255)->helperText('Use the ad account ID from the network, not your application ID.'),
                        TextInput::make('access_token')->label('Workspace ad-account access token')->password()->autocomplete('new-password')
                            ->formatStateUsing(fn () => null)->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create' && app(PlatformConfiguration::class)->provider() === 'direct')
                            ->visible(fn () => app(PlatformConfiguration::class)->provider() === 'direct')
                            ->helperText('A token for your own advertising account with ad-management permission. Leave blank when editing to keep the saved token. Platform application secrets are managed by the super admin.'),
                        Select::make('metadata.zernio_account_id')->label('Connected Zernio account')
                            ->options(function (): array {
                                if (app(PlatformConfiguration::class)->provider() !== 'zernio' || ! Filament::getTenant()) {
                                    return [];
                                }
                                try {
                                    $result = app(ZernioTenantService::class)->accounts(Filament::getTenant());

                                    return collect(data_get($result, 'accounts', data_get($result, 'data.accounts', [])))->filter(fn ($row) => is_array($row) && isset($row['_id']))
                                        ->mapWithKeys(fn ($row) => [$row['_id'] => ucfirst($row['platform'] ?? '').' — '.($row['displayName'] ?? $row['username'] ?? $row['_id'])])->all();
                                } catch (\Throwable $exception) {
                                    report($exception);

                                    return [];
                                }
                            })->searchable()->required(fn () => app(PlatformConfiguration::class)->provider() === 'zernio')
                            ->visible(fn () => app(PlatformConfiguration::class)->provider() === 'zernio'),
                        Select::make('metadata.currency_exponent')->label('Account currency decimal places')->options([0 => '0 — e.g. JPY', 2 => '2 — e.g. USD, GBP, EUR', 3 => '3 — e.g. KWD'])->default(2)
                            ->visible(fn () => app(PlatformConfiguration::class)->provider() === 'direct')->helperText('Match the currency of this advertising account before setting a budget.'),
                        Actions::make([Action::make('manage_connections')->label('Manage workspace connections')->url(fn () => MarketingConnections::getUrl())]),
                        Toggle::make('status')
                            ->label('Active')
                            ->default(true)
                            ->visible(fn ($record) => $record),
                        DateTimePicker::make('last_sync')
                            ->disabled()
                            ->visible(fn ($record) => $record),

                    ]),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                BadgeColumn::make('platform')->colors([
                    'primary' => 'Google Ads',
                    'success' => 'Facebook Ads',
                    'warning' => 'LinkedIn Ads',
                    'danger' => 'Microsoft Ads',
                ]),
                TextColumn::make('account_id')->searchable(),
                BooleanColumn::make('status'),
                TextColumn::make('last_sync')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('platform')
                    ->options([
                        'Google Ads' => 'Google Ads',
                        'Facebook Ads' => 'Facebook Ads',
                        'LinkedIn Ads' => 'LinkedIn Ads',
                        'Microsoft Ads' => 'Microsoft Ads',
                    ]),
                // BooleanFilter::make('status'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('refresh_token')
                    ->label('Refresh Token')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn ($record) => $record->refreshToken())
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            CampaignsRelationManager::class,
            AdSetsRelationManager::class,
            AdsRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListAdvertisingAccounts::route('/'),
            'view' => ViewAdvertisingAccount::route('/{record}'),
            'create' => CreateAdvertisingAccount::route('/create'),
            'edit' => EditAdvertisingAccount::route('/{record}/edit'),
        ];
    }
}
