<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Team;
use App\Models\User;
use App\Services\Marketing\PlatformConfiguration;
use App\Services\Marketing\WorkspaceAccess;
use App\Services\Marketing\WorkspaceOnboarding;
use App\Services\Zernio\ZernioTenantService;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

/** @property Schema $form */
class SetupWizard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings & integrations';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Workspace setup';

    protected static ?string $title = 'Welcome to your workspace';

    protected string $view = 'filament.app.pages.setup-wizard';

    public array $data = [];

    public bool $saved = false;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        $team = Filament::getTenant();

        return $user instanceof User && $team instanceof Team && app(WorkspaceAccess::class)->canManage($user, $team);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
        $team = Filament::getTenant();
        $preferences = $team->getAttribute('marketing_preferences') ?? [];
        $this->form->fill([
            'team_name' => $team->getAttribute('name'),
            'account_name' => Filament::auth()->user()->getAttribute('name'),
            'account_email' => Filament::auth()->user()->getAttribute('email'),
            'timezone' => $preferences['timezone'] ?? config('app.timezone', 'UTC'),
            'brand_name' => $preferences['brand_name'] ?? $team->getAttribute('name'),
            'brand_website' => $preferences['brand_website'] ?? null,
            'audience' => $preferences['audience'] ?? null,
            'tone' => $preferences['tone'] ?? 'professional',
            'platforms' => $preferences['platforms'] ?? ['facebook', 'linkedin'],
            'approval_required' => $preferences['approval_required'] ?? true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Wizard::make([
                Step::make('Your workspace')->description('Account and workspace identity')->schema([
                    Placeholder::make('welcome')->content('Set up your workspace, define your brand, then connect the accounts you want to manage. Your administrator manages platform credentials.'),
                    TextInput::make('account_name')->label('Your name')->required()->maxLength(255),
                    TextInput::make('account_email')->label('Email address')->email()->required()->maxLength(255),
                    TextInput::make('team_name')->label('Workspace name')->required()->maxLength(255),
                    Select::make('timezone')->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))->searchable()->required()->helperText('The content calendar and publishing schedules use this timezone.'),
                ]),
                Step::make('Brand and audience')->description('Give your team and AI a consistent brief')->schema([
                    TextInput::make('brand_name')->required()->maxLength(180),
                    TextInput::make('brand_website')->url()->helperText('Use your public HTTPS website.'),
                    Textarea::make('audience')->label('Target audience')->maxLength(2000)->rows(4),
                    Select::make('tone')->options(['professional' => 'Professional', 'friendly' => 'Friendly', 'informative' => 'Informative', 'bold' => 'Bold'])->required(),
                ]),
                Step::make('Channels and review')->description('Choose channels and publishing controls')->schema([
                    Placeholder::make('provider')->label('Connection service')->content(fn () => app(PlatformConfiguration::class)->provider() === 'zernio' ? 'Zernio — connect your accounts from the Marketing workspace after setup.' : 'Standalone APIs — connect your accounts from Connected accounts after setup.'),
                    CheckboxList::make('platforms')->label('Channels to manage')->options(array_combine(ZernioTenantService::PLATFORMS, array_map('ucfirst', ZernioTenantService::PLATFORMS)))->columns(3)->required(),
                    Toggle::make('approval_required')->label('Require review before publishing')->default(true)->helperText('AI-generated posts always start as drafts. Reviewers must approve scheduled posts when this setting is enabled.'),
                    Placeholder::make('finish')->content('Saving completes workspace setup. Publishing and paid campaigns require connected accounts and a separate explicit action.'),
                ]),
            ])->submitAction(view('filament.app.pages.setup-wizard-submit')),
        ]);
    }

    public function save(): void
    {
        abort_unless(static::canAccess(), 403);
        $data = $this->form->getState();
        try {
            app(WorkspaceOnboarding::class)->save(Filament::auth()->user(), Filament::getTenant(), $data);
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages(collect($exception->errors())->mapWithKeys(fn ($messages, $key) => ['data.'.$key => $messages])->all());
        } catch (QueryException $exception) {
            report($exception);
            Notification::make()->title('Setup could not be saved')->body('Your form has been kept. Please try again.')->danger()->send();

            return;
        }
        $this->saved = true;
        Notification::make()->title('Your workspace is ready')->body('Connect your social accounts, create a draft, and review it before publishing.')->success()->send();
    }
}
