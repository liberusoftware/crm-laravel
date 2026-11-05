<?php

namespace App\Filament\App\Resources;

use App\Filament\Actions\ContactDialingActions;
use App\Filament\App\Resources\LeadResource\Pages;
use App\Filament\App\Resources\LeadResource\Pages\LeadQualityReport;
use App\Filament\App\Resources\LeadResource\Pages\ViewLead;
use App\Filament\Concerns\EnforcesResourcePermissions;
use App\Filament\Exports\LeadExporter;
use App\Models\Lead;
use App\Support\AccessContext;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\CRM\DialerAndOutreach\Queries\ContactCallHistory;

class LeadResource extends Resource
{
    use EnforcesResourcePermissions;

    protected static ?string $model = Lead::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lead overview')
                    ->description('Identify the contact and track qualification progress.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Select::make('contact_id')
                            ->label('Contact')
                            ->relationship('contact', 'name')
                            ->searchable()
                            ->helperText('Link a contact so this lead is easy to identify.'),
                        Forms\Components\Select::make('status')
                            ->default('new')
                            ->options([
                                'new' => 'New',
                                'contacted' => 'Contacted',
                                'qualified' => 'Qualified',
                                'lost' => 'Lost',
                            ])
                            ->required(),
                        Forms\Components\Select::make('source')
                            ->options([
                                'website' => 'Website',
                                'referral' => 'Referral',
                                'social_media' => 'Social Media',
                                'direct' => 'Direct',
                                'other' => 'Other',
                            ]),
                        // Masked-role viewers get the read-only placeholder on edit; the
                        // hidden real field isn't validated or dehydrated, so a save keeps
                        // the stored value.
                        Forms\Components\TextInput::make('potential_value')
                            ->numeric()
                            ->prefix('$')
                            ->visible(fn (string $operation): bool => $operation === 'create' || ! AccessContext::shouldMaskFields()),
                        Forms\Components\Placeholder::make('potential_value_masked')
                            ->label('Potential Value')
                            ->content('[hidden]')
                            ->visible(fn (string $operation): bool => $operation !== 'create' && AccessContext::shouldMaskFields()),
                        Forms\Components\DatePicker::make('expected_close_date'),
                    ]),
                Section::make('Qualification details')
                    ->description('Fine-tune scoring and lifecycle classification.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->label('Lead Score'),
                        Forms\Components\Select::make('lifecycle_stage')
                            ->options([
                                'subscriber' => 'Subscriber',
                                'lead' => 'Lead',
                                'marketing_qualified_lead' => 'Marketing Qualified Lead',
                                'sales_qualified_lead' => 'Sales Qualified Lead',
                                'opportunity' => 'Opportunity',
                            ]),
                    ]),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['contact' => fn (BelongsTo $contacts): Builder => app(ContactCallHistory::class)->withLastCalled($contacts->getQuery())]))
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->striped()
            ->emptyStateHeading('No leads found')
            ->emptyStateDescription('Create a lead to get started, or adjust your search and filters.')
            ->columns([
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact')
                    ->placeholder('No contact linked')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'qualified' => 'success',
                        'contacted' => 'info',
                        'new' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (?string $state): string => str($state ?? '')->replace('_', ' ')->title()->toString())
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('potential_value')
                    ->formatStateUsing(fn (?string $state): string => AccessContext::shouldMaskFields()
                        ? '[hidden]'
                        : '$'.number_format((float) $state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->sortable()
                    ->label('Lead Score'),
                Tables\Columns\TextColumn::make('lifecycle_stage')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (?string $state): string => str($state ?? '')->replace('_', ' ')->title()->toString())
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_close_date')
                    ->label('Expected close')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_called')
                    ->label('Last called')
                    ->getStateUsing(fn (Lead $record): ?string => $record->contact === null ? null : app(ContactCallHistory::class)->lastCalled($record->contact))
                    ->dateTime()
                    ->placeholder('Never called'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                ContactDialingActions::lastCalledFilter(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'qualified' => 'Qualified',
                        'lost' => 'Lost',
                    ]),
                Tables\Filters\Filter::make('score')
                    ->schema([
                        Forms\Components\TextInput::make('score_from')->label('Minimum score')->numeric()->minValue(0)->maxValue(100),
                        Forms\Components\TextInput::make('score_to')->label('Maximum score')->numeric()->minValue(0)->maxValue(100),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            isset($data['score_from']) && $data['score_from'] !== '',
                            fn (Builder $query): Builder => $query->where('score', '>=', $data['score_from']),
                        )
                        ->when(
                            isset($data['score_to']) && $data['score_to'] !== '',
                            fn (Builder $query): Builder => $query->where('score', '<=', $data['score_to']),
                        )),
            ])
            ->headerActions([
                // Gated off for masked (`free`) roles: a CSV would otherwise
                // bypass the potential_value masking applied in the UI.
                ExportAction::make()
                    ->exporter(LeadExporter::class)
                    ->visible(fn (): bool => ! AccessContext::shouldMaskFields()),
            ])
            ->recordActions([
                ContactDialingActions::quick(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
            'quality-report' => LeadQualityReport::route('/quality-report'),
        ];
    }
}
