<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Enums\SocialPlatform;
use App\Filament\App\Resources\CampaignResource\Pages\CreateCampaign;
use App\Filament\App\Resources\CampaignResource\Pages\EditCampaign;
use App\Filament\App\Resources\CampaignResource\Pages\ListCampaigns;
use App\Filament\App\Resources\CampaignResource\Pages\ViewCampaign;
use App\Filament\Concerns\EnforcesResourcePermissions;
use App\Filament\Exports\CampaignExporter;
use App\Models\Campaign;
use App\Services\Marketing\AdvertisingManager;
use App\Services\Marketing\ContentAssistant;
use App\Support\AccessContext;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CampaignResource extends Resource
{
    use EnforcesResourcePermissions;

    protected static ?string $model = Campaign::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('advertising_account_id')
                    ->relationship('advertisingAccount', 'name', modifyQueryUsing: fn ($query) => $query->where('team_id', Filament::getTenant()?->getKey()))
                    ->required()
                    ->label('Advertising Account'),
                TextInput::make('name')
                    ->hintAction(Action::make('generateAdCopy')->label('AI campaign brief')->schema([
                        Textarea::make('brief')->label('Offer, audience and campaign goal')->required()->maxLength(5000),
                        Select::make('platform')->options(SocialPlatform::options())->required(),
                    ])->action(function (array $data, callable $set): void {
                        try {
                            $copy = app(ContentAssistant::class)->generate(Filament::auth()->user(), Filament::getTenant(), $data + ['purpose' => 'advertisement']);
                            $set('metadata.ad_copy', $copy['content']);
                            $set('metadata.headline', $copy['headline'] ?? '');
                            $set('metadata.call_to_action', $copy['call_to_action'] ?? '');
                        } catch (\Throwable $exception) {
                            report($exception);
                            Notification::make()->title('Ad copy could not be generated')->body('Your current campaign draft has been kept.')->danger()->send();
                        }
                    }))
                    ->required()
                    ->maxLength(255),
                TextInput::make('external_id')
                    ->maxLength(255)
                    ->label('External ID'),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'archived' => 'Archived',
                        'deleted' => 'Deleted',
                    ])
                    ->default('paused')
                    ->required(),
                Select::make('objective')
                    ->options([
                        'awareness' => 'Awareness',
                        'consideration' => 'Consideration',
                        'conversion' => 'Conversion',
                    ]),
                // Masked-role viewers get the read-only placeholder on edit; the
                // hidden real field isn't validated or dehydrated, so a save keeps
                // the stored value.
                TextInput::make('budget')
                    ->numeric()
                    ->prefix('$')
                    ->visible(fn (string $operation): bool => $operation === 'create' || ! AccessContext::shouldMaskFields()),
                Placeholder::make('budget_masked')
                    ->label('Budget')
                    ->content('[hidden]')
                    ->visible(fn (string $operation): bool => $operation !== 'create' && AccessContext::shouldMaskFields()),
                Select::make('budget_type')
                    ->options([
                        'daily' => 'Daily',
                        'lifetime' => 'Lifetime',
                    ])
                    ->default('daily'),
                DateTimePicker::make('start_date'),
                DateTimePicker::make('end_date')->after('start_date'),
                Textarea::make('metadata.ad_copy')->label('Ad copy draft')->maxLength(10000)->rows(5)->helperText('Review generated claims, pricing, and brand language before creating an ad.'),
                TextInput::make('metadata.headline')->label('Ad headline')->maxLength(255),
                TextInput::make('metadata.call_to_action')->label('Call to action')->maxLength(255),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('advertisingAccount.name')->label('Account')->searchable(),
                BadgeColumn::make('status')->colors([
                    'success' => 'active',
                    'warning' => 'paused',
                    'secondary' => 'archived',
                    'danger' => 'deleted',
                ]),
                BadgeColumn::make('objective'),
                TextColumn::make('budget')
                    ->formatStateUsing(fn (?string $state): string => AccessContext::shouldMaskFields()
                        ? '[hidden]'
                        : '$'.number_format((float) $state, 2)),
                TextColumn::make('start_date')->dateTime(),
                TextColumn::make('end_date')->dateTime(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'archived' => 'Archived',
                        'deleted' => 'Deleted',
                    ]),
                SelectFilter::make('objective')
                    ->options([
                        'awareness' => 'Awareness',
                        'consideration' => 'Consideration',
                        'conversion' => 'Conversion',
                    ]),
            ])
            ->headerActions([
                // Hidden for masked (`free`) roles so a CSV can't bypass budget masking.
                ExportAction::make()
                    ->exporter(CampaignExporter::class)
                    ->visible(fn (): bool => ! AccessContext::shouldMaskFields()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('createRemote')->label('Create paused campaign')->requiresConfirmation()->modalDescription('Creates a paused campaign at the selected provider. No advertising starts until you explicitly activate it.')
                    ->visible(fn (Campaign $record): bool => blank($record->external_id) && ! AccessContext::shouldMaskFields() && static::canEdit($record))
                    ->action(fn (Campaign $record) => self::manageCampaign($record, 'create')),
                Action::make('syncRemote')->label('Sync campaign settings')->requiresConfirmation()
                    ->visible(fn (Campaign $record): bool => filled($record->external_id) && ! AccessContext::shouldMaskFields() && static::canEdit($record))
                    ->action(fn (Campaign $record) => self::manageCampaign($record, 'update')),
                Action::make('activateRemote')->label('Activate campaign')->color('success')->requiresConfirmation()->modalDescription('Activating a campaign can incur advertising charges at the configured budget.')
                    ->visible(fn (Campaign $record): bool => filled($record->external_id) && $record->status === 'paused' && ! AccessContext::shouldMaskFields() && static::canEdit($record))
                    ->action(fn (Campaign $record) => self::manageCampaign($record, 'activate')),
                Action::make('pauseRemote')->label('Pause campaign')->requiresConfirmation()
                    ->visible(fn (Campaign $record): bool => filled($record->external_id) && $record->status === 'active' && ! AccessContext::shouldMaskFields() && static::canEdit($record))
                    ->action(fn (Campaign $record) => self::manageCampaign($record, 'pause')),
                Action::make('archiveRemote')->label('Archive at provider')->color('danger')->requiresConfirmation()
                    ->visible(fn (Campaign $record): bool => filled($record->external_id) && ! AccessContext::shouldMaskFields() && static::canEdit($record))
                    ->action(fn (Campaign $record) => self::manageCampaign($record, 'archive')),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    private static function manageCampaign(Campaign $campaign, string $operation): void
    {
        try {
            app(AdvertisingManager::class)->execute(Filament::auth()->user(), Filament::getTenant(), $campaign, $operation);
            Notification::make()->title('Campaign updated at provider')->success()->send();
        } catch (\Throwable $exception) {
            report($exception);
            Notification::make()->title('Campaign could not be updated')->body('Check the connected account and campaign configuration. If creation timed out, reconcile with the provider before retrying.')->danger()->send();
        }
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'view' => ViewCampaign::route('/{record}'),
            'edit' => EditCampaign::route('/{record}/edit'),
        ];
    }
}
