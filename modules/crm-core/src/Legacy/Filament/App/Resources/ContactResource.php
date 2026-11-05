<?php

namespace App\Filament\App\Resources;

use App\Actions\Portal\InvitePortalCustomer;
use App\Actions\Portal\RevokePortalCustomer;
use App\Exceptions\PortalOnboardingException;
use App\Filament\Actions\ContactDialingActions;
use App\Filament\App\Resources\ContactResource\Pages\CreateContact;
use App\Filament\App\Resources\ContactResource\Pages\EditContact;
use App\Filament\App\Resources\ContactResource\Pages\ListContacts;
use App\Filament\App\Resources\ContactResource\Pages\ViewContact;
use App\Filament\App\Resources\ContactResource\RelationManagers\DocumentsRelationManager;
use App\Filament\Concerns\EnforcesResourcePermissions;
use App\Filament\Exports\ContactExporter;
use App\Models\Company;
use App\Models\Contact;
use App\Services\TwilioService;
use App\Support\AccessContext;
use App\Support\PortalCustomer;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Liberu\CRM\DialerAndOutreach\Queries\ContactCallHistory;

class ContactResource extends Resource
{
    use EnforcesResourcePermissions;

    protected static ?string $model = Contact::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact details')
                    ->description('Start with the person and how to reach them.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->maxLength(255),
                        // On edit, a masked-role viewer gets the read-only placeholder below
                        // instead of the real input. The hidden real field is neither
                        // validated nor dehydrated, so a save preserves the stored value.
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->visible(fn (string $operation): bool => $operation === 'create' || ! AccessContext::shouldMaskFields()),
                        Placeholder::make('email_masked')
                            ->label('Email')
                            ->content('[hidden]')
                            ->visible(fn (string $operation): bool => $operation !== 'create' && AccessContext::shouldMaskFields()),
                        TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->visible(fn (string $operation): bool => $operation === 'create' || ! AccessContext::shouldMaskFields()),
                        Placeholder::make('phone_masked')
                            ->label('Phone number')
                            ->content('[hidden]')
                            ->visible(fn (string $operation): bool => $operation !== 'create' && AccessContext::shouldMaskFields()),
                    ]),
                Section::make('Relationship')
                    ->description('Track status, source and the linked company.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'lead' => 'Lead',
                                'prospect' => 'Prospect',
                            ]),
                        Select::make('source')
                            ->options([
                                'website' => 'Website',
                                'referral' => 'Referral',
                                'social_media' => 'Social Media',
                                'direct' => 'Direct',
                                'other' => 'Other',
                            ]),
                        Select::make('lifecycle_stage')
                            ->options([
                                'subscriber' => 'Subscriber',
                                'lead' => 'Lead',
                                'marketing_qualified_lead' => 'Marketing Qualified Lead',
                                'sales_qualified_lead' => 'Sales Qualified Lead',
                                'opportunity' => 'Opportunity',
                                'customer' => 'Customer',
                                'evangelist' => 'Evangelist',
                            ]),
                        Select::make('company_id')
                            ->label('Company')
                            ->options(Company::pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                    ]),
                Section::make('Company insights')
                    ->description('Optional details for segmentation and account planning.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Select::make('industry')
                            ->options([
                                'Technology' => 'Technology',
                                'Healthcare' => 'Healthcare',
                                'Finance' => 'Finance',
                                'Education' => 'Education',
                                'Retail' => 'Retail',
                                'Manufacturing' => 'Manufacturing',
                                'Real Estate' => 'Real Estate',
                                'Other' => 'Other',
                            ]),
                        TextInput::make('company_size')
                            ->numeric(),
                        TextInput::make('annual_revenue')
                            ->numeric()
                            ->prefix('$'),

                    ]),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => app(ContactCallHistory::class)->withLastCalled($query))
            ->searchPlaceholder('Search names or a full email address')
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->striped()
            ->emptyStateHeading('No contacts found')
            ->emptyStateDescription('Create a contact to get started, or adjust your search and filters.')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->formatStateUsing(fn (?string $state, Contact $record): mixed => $record->maskFor('email', $state))
                    // email is encrypted at rest, so search matches the blind index
                    // (exact, full-email only). Still gated off for masked viewers.
                    ->searchable(
                        condition: ! AccessContext::shouldMaskFields(),
                        query: fn (Builder $query, string $search): Builder => $query->where('email_hash', Contact::hashEmail($search)),
                    ),
                TextColumn::make('phone_number')
                    // Encrypted at rest — not searchable (no blind index for phone).
                    ->formatStateUsing(fn (?string $state, Contact $record): mixed => $record->maskFor('phone_number', $state)),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'lead' => 'warning',
                        'prospect' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('lifecycle_stage')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (?string $state): string => str($state ?? '')->replace('_', ' ')->title()->toString())
                    ->label('Lifecycle Stage')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Company')
                    ->sortable(),
                TextColumn::make('last_called')
                    ->label('Last called')
                    ->getStateUsing(fn (Contact $record): ?string => app(ContactCallHistory::class)->lastCalled($record))
                    ->dateTime()
                    ->placeholder('Never called'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                ContactDialingActions::lastCalledFilter(),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'lead' => 'Lead',
                        'prospect' => 'Prospect',
                    ]),
                SelectFilter::make('source')
                    ->options([
                        'website' => 'Website',
                        'referral' => 'Referral',
                        'social_media' => 'Social Media',
                        'direct' => 'Direct',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('lifecycle_stage')
                    ->label('Lifecycle Stage')
                    ->options([
                        'subscriber' => 'Subscriber',
                        'lead' => 'Lead',
                        'marketing_qualified_lead' => 'Marketing Qualified Lead',
                        'sales_qualified_lead' => 'Sales Qualified Lead',
                        'opportunity' => 'Opportunity',
                        'customer' => 'Customer',
                        'evangelist' => 'Evangelist',
                    ]),
                SelectFilter::make('industry')
                    ->options([
                        'Technology' => 'Technology',
                        'Healthcare' => 'Healthcare',
                        'Finance' => 'Finance',
                        'Education' => 'Education',
                        'Retail' => 'Retail',
                        'Manufacturing' => 'Manufacturing',
                        'Real Estate' => 'Real Estate',
                        'Other' => 'Other',
                    ]),
            ])
            ->headerActions([
                ContactDialingActions::manage(),
                // Gated off for masked (`free`) roles: a CSV would otherwise
                // bypass the email/phone_number masking applied in the UI.
                ExportAction::make()
                    ->exporter(ContactExporter::class)
                    ->visible(fn (): bool => ! AccessContext::shouldMaskFields()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('sendSMS')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Textarea::make('message')
                            ->label('SMS Message')
                            ->required(),
                    ])
                    ->action(function (Contact $record, array $data, TwilioService $twilioService): void {
                        $result = $twilioService->sendSMS($record->phone_number, $data['message']);
                        if ($result) {
                            Notification::make()->title('SMS sent successfully')->success()->send();
                        } else {
                            Notification::make()->title('Failed to send SMS')->danger()->send();
                        }
                    }),
                ContactDialingActions::quick(),
                Action::make('inviteToPortal')
                    ->label('Invite to portal')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn (Contact $record): bool => filled($record->email))
                    ->requiresConfirmation()
                    ->action(function (Contact $record, InvitePortalCustomer $invite): void {
                        try {
                            $invite($record);
                            Notification::make()->title('Portal invitation sent')->success()->send();
                        } catch (PortalOnboardingException $e) {
                            Notification::make()->title('Could not invite')->body($e->getMessage())->danger()->send();
                        }
                    }),
                Action::make('revokePortalAccess')
                    ->label('Revoke portal access')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->visible(fn (Contact $record): bool => PortalCustomer::existsForEmail($record->email))
                    ->requiresConfirmation()
                    ->action(function (Contact $record, RevokePortalCustomer $revoke): void {
                        try {
                            $revoke($record);
                            Notification::make()->title('Portal access revoked')->success()->send();
                        } catch (PortalOnboardingException $e) {
                            Notification::make()->title('Could not revoke')->body($e->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                BulkAction::make('bulkSendSMS')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Textarea::make('message')
                            ->label('SMS Message')
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data, TwilioService $twilioService): void {
                        $successCount = 0;
                        $failCount = 0;
                        foreach ($records as $record) {
                            $result = $twilioService->sendSMS($record->phone_number, $data['message']);
                            $result ? $successCount++ : $failCount++;
                        }
                        Notification::make()
                            ->title("Bulk SMS sent: {$successCount} successful, {$failCount} failed")
                            ->send();
                    }),
                ContactDialingActions::bulk(),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListContacts::route('/'),
            'create' => CreateContact::route('/create'),
            'view' => ViewContact::route('/{record}'),
            'edit' => EditContact::route('/{record}/edit'),
        ];
    }
}
