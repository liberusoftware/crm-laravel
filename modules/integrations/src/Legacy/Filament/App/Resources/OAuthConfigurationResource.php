<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\OAuthConfigurationResource\Pages\CreateOAuthConfiguration;
use App\Filament\App\Resources\OAuthConfigurationResource\Pages\EditOAuthConfiguration;
use App\Filament\App\Resources\OAuthConfigurationResource\Pages\ListOAuthConfigurations;
use App\Filament\Concerns\EnforcesResourcePermissions;
use App\Models\OAuthConfiguration;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OAuthConfigurationResource extends Resource
{
    use EnforcesResourcePermissions;

    public static function permissionResource(): string
    {
        return 'oauth_configuration';
    }

    protected static ?string $model = OAuthConfiguration::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings & integrations';

    protected static ?string $navigationLabel = 'Connected services';

    protected static ?int $navigationSort = 2;

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_name')
                    ->options([
                        'facebook' => 'Facebook', 'google' => 'Google', 'linkedin' => 'LinkedIn',
                        'twitter' => 'X / Twitter', 'microsoft' => 'Microsoft 365', 'mailchimp' => 'Mailchimp',
                        'twilio' => 'Twilio', 'whatsapp' => 'WhatsApp', 'helpdesk' => 'Helpdesk', 'zernio' => 'Zernio',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('account_name')->label('Connection name')->maxLength(255),
                TextInput::make('client_id')
                    ->required()
                    ->maxLength(255),
                TextInput::make('client_secret')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(65535),
                Toggle::make('is_active')->default(true),
                KeyValue::make('additional_settings')->label('Additional settings'),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service_name'),
                TextColumn::make('client_id'),
                TextColumn::make('created_at')
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListOAuthConfigurations::route('/'),
            'create' => CreateOAuthConfiguration::route('/create'),
            'edit' => EditOAuthConfiguration::route('/{record}/edit'),
        ];
    }
}
