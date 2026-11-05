<?php

namespace App\Filament\App\Resources;

use App\Enums\SocialPlatform;
use App\Filament\App\Resources\SocialMediaPostResource\Pages\CreateSocialMediaPost;
use App\Filament\App\Resources\SocialMediaPostResource\Pages\EditSocialMediaPost;
use App\Filament\App\Resources\SocialMediaPostResource\Pages\ListSocialMediaPosts;
use App\Filament\App\Resources\SocialMediaPostResource\Pages\ViewSocialMediaPost;
use App\Filament\Concerns\EnforcesResourcePermissions;
use App\Models\SocialMediaPost;
use App\Models\Team;
use App\Services\Marketing\ContentAssistant;
use App\Services\Marketing\PostTargets;
use App\Services\Marketing\PostWorkflow;
use App\Services\Marketing\WorkspaceAccess;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SocialMediaPostResource extends Resource
{
    use EnforcesResourcePermissions;

    protected static ?string $model = SocialMediaPost::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-share';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 2;

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Post Content')
                            ->schema([
                                Textarea::make('content')
                                    ->required()
                                    ->maxLength(65535)
                                    ->helperText('Write your post content here. Character limits vary by platform.')
                                    ->hintAction(Action::make('assist')->label('AI writing assistant')->schema([
                                        Textarea::make('brief')->label('What should this post achieve?')->required()->maxLength(5000),
                                        Select::make('platform')->options(SocialPlatform::options())->required(),
                                    ])->action(function (array $data, callable $set): void {
                                        try {
                                            $result = app(ContentAssistant::class)->generate(Filament::auth()->user(), Filament::getTenant(), $data + ['purpose' => 'post']);
                                            $set('content', $result['content']);
                                        } catch (\Throwable $exception) {
                                            report($exception);
                                            Notification::make()->title('Content could not be generated')->body('Your current draft has been kept. Please check the brief or try again later.')->danger()->send();
                                        }
                                    }))
                                    ->live()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('character_count', strlen((string) $state))),
                                TextInput::make('character_count')
                                    ->label('Character Count')
                                    ->disabled()
                                    ->dehydrated(false),
                                DateTimePicker::make('scheduled_at')
                                    ->timezone(fn () => data_get(Filament::getTenant()?->getAttribute('marketing_preferences'), 'timezone', 'UTC'))
                                    ->helperText('Schedule when this post should be published')
                                    ->minDate(now())
                                    ->withoutSeconds(),
                                MultiSelect::make('platforms')
                                    ->options(fn () => Filament::getTenant() instanceof Team ? app(PostTargets::class)->options(Filament::getTenant()) : SocialPlatform::options())
                                    ->required()
                                    ->helperText('Select the platforms where you want to publish')
                                    ->columns(2),
                                Select::make('status')
                                    ->options(SocialMediaPost::getStatuses())
                                    ->default('draft')->disabled()->dehydrated(false),
                            ])
                            ->columnSpan(2),

                        Section::make('Media & Links')
                            ->schema([
                                TextInput::make('link')
                                    ->url()
                                    ->label('Link (optional)')
                                    ->helperText('Add a URL to your post'),
                                FileUpload::make('image_path')
                                    ->image()
                                    ->label('Image (optional)')
                                    ->disk('public')
                                    ->directory('social-media-posts')
                                    ->imagePreviewHeight('250')
                                    ->maxSize(5120)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                                    ->helperText('Upload images (max 5MB, JPG/PNG/GIF). Supported by Facebook, Twitter/X, Instagram, LinkedIn.'),
                                TextInput::make('video_url')
                                    ->url()
                                    ->label('Video URL (optional)')
                                    ->helperText('Add a video URL. Supported by Facebook, Twitter/X, Instagram, LinkedIn, YouTube.'),
                            ])
                            ->columnSpan(2),

                        Section::make('Preview')
                            ->schema([
                                View::make('filament.components.social-media-preview')
                                    ->visible(fn ($get): bool => ! empty($get('content'))),
                            ])
                            ->columnSpan(2),

                        Section::make('Analytics')
                            ->schema([
                                Placeholder::make('Analytics')
                                    ->content(fn (SocialMediaPost $record) => view('filament.components.social-media-analytics', [
                                        'post' => $record,
                                        'detailed' => true,
                                    ])),
                                View::make('filament.components.engagement-chart')
                                    ->visible(fn ($record): bool => $record && $record->status === SocialMediaPost::STATUS_PUBLISHED),
                            ])
                            ->columnSpan(1)
                            ->hidden(fn ($record): bool => $record === null || $record->status !== SocialMediaPost::STATUS_PUBLISHED),
                    ]),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content')->limit(50),
                TagsColumn::make('platforms')->state(fn (SocialMediaPost $record) => array_map(fn ($target) => is_array($target) ? $target['platform'] : $target, $record->platforms)),
                TextColumn::make('scheduled_at')
                    ->dateTime(),
                BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'draft',
                        'warning' => 'scheduled',
                        'success' => 'published',
                        'danger' => ['failed', 'publishing_unknown'],
                        'info' => 'publishing',
                        'gray' => 'cancelled',
                    ]),
                TextColumn::make('likes')
                    ->sortable(),
                TextColumn::make('shares')
                    ->sortable(),
                TextColumn::make('comments')
                    ->sortable(),
                TextColumn::make('last_error')->label('Delivery note')->limit(100)->wrap()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(SocialMediaPost::getStatuses()),
                SelectFilter::make('platforms')
                    ->options(SocialPlatform::options())
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')->label('Approve content')->requiresConfirmation()
                    ->visible(fn (SocialMediaPost $record): bool => in_array($record->status, ['draft', 'scheduled'], true) && app(WorkspaceAccess::class)->canManage(Filament::auth()->user(), Filament::getTenant()))
                    ->action(fn (SocialMediaPost $record) => app(PostWorkflow::class)->approve(Filament::auth()->user(), Filament::getTenant(), $record)),
                Action::make('schedule')->label('Schedule post')->schema([
                    DateTimePicker::make('scheduled_at')->required()->minDate(now())->timezone(fn () => data_get(Filament::getTenant()?->getAttribute('marketing_preferences'), 'timezone', 'UTC')),
                ])->visible(fn (SocialMediaPost $record): bool => in_array($record->status, ['draft', 'scheduled'], true))
                    ->action(fn (SocialMediaPost $record, array $data) => app(PostWorkflow::class)->schedule(Filament::auth()->user(), Filament::getTenant(), $record, $data['scheduled_at'])),
                Action::make('reconcile')->label('Reconcile delivery')->schema([
                    Select::make('resolution')->options(['published' => 'Confirmed published at provider', 'failed' => 'Confirmed not published at provider'])->required(),
                    Textarea::make('evidence')->label('Provider verification and reference')->required()->minLength(10)->maxLength(1500),
                ])->modalDescription('Check every target account before resolving an uncertain delivery. This action records your decision and does not send the post again.')
                    ->visible(fn (SocialMediaPost $record): bool => $record->status === 'publishing_unknown' && app(WorkspaceAccess::class)->canManage(Filament::auth()->user(), Filament::getTenant()))
                    ->action(fn (SocialMediaPost $record, array $data) => app(PostWorkflow::class)->reconcile(Filament::auth()->user(), Filament::getTenant(), $record, $data)),
                Action::make('cancel')->label('Cancel scheduled post')->requiresConfirmation()->color('danger')
                    ->visible(fn (SocialMediaPost $record): bool => $record->status === 'scheduled')
                    ->action(function (SocialMediaPost $record): void {
                        abort_unless(static::canEdit($record), 403);
                        SocialMediaPost::query()->whereKey($record->getKey())->where('status', 'scheduled')->update(['status' => 'cancelled']);
                    }),
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
            'index' => ListSocialMediaPosts::route('/'),
            'view' => ViewSocialMediaPost::route('/{record}'),
            'create' => CreateSocialMediaPost::route('/create'),
            'edit' => EditSocialMediaPost::route('/{record}/edit'),
        ];
    }
}
