<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\User;
use App\Services\Marketing\PlatformConfiguration;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

/** @property Schema $form */
final class SetupWizard extends Page
{
    protected static ?string $title = 'Platform setup';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected string $view = 'filament.app.pages.setup-wizard';

    public array $data = [];

    public bool $saved = false;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User && $user->isSuperAdmin();
    }

    public function mount(): void
    {
        abort_unless(self::canAccess(), 403);
        $configuration = app(PlatformConfiguration::class);
        $this->data = ['provider' => $configuration->provider(), 'ai_enabled' => (bool) data_get($configuration->settings()?->getAttribute('options'), 'ai_enabled', false)];
        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        $applications = [];
        foreach (['facebook' => 'Meta / Facebook', 'google' => 'Google', 'linkedin' => 'LinkedIn', 'twitter' => 'X / Twitter', 'microsoft' => 'Microsoft 365', 'twilio' => 'Twilio', 'whatsapp' => 'WhatsApp', 'mailchimp' => 'Mailchimp'] as $key => $label) {
            $applications[] = Section::make($label)->schema([
                TextInput::make($key.'_client_id')->label('Application / account ID')->maxLength(1000),
                TextInput::make($key.'_client_secret')->label('Application secret / API token')->password()->autocomplete('new-password')->helperText('Leave blank to retain the saved value.'),
            ])->columns(2);
        }

        return $schema->statePath('data')->components([
            Wizard::make([
                Step::make('Platform')->description('Choose how the service connects')->schema([
                    Placeholder::make('scope')->content('These settings apply to every workspace. Only super admins can change platform applications, API credentials, and AI services.'),
                    Select::make('provider')->label('Social publishing and advertising provider')->options(['direct' => 'Standalone platform APIs', 'zernio' => 'Zernio — unified management'])->required()->live()
                        ->helperText('Workspace users connect their own accounts through the selected provider. Provider failures never fall back to another provider.'),
                    Placeholder::make('provider_coverage')->label('Provider coverage')->content(fn ($get) => $get('provider') === 'direct' ? 'Standalone publishing supports Facebook and Instagram images, X text, and LinkedIn text. Campaign management supports Meta. Choose Zernio for broader network and media coverage.' : 'Zernio handles connected publishing channels and advertising networks through one integration. Each network still requires its own permissions and supported campaign settings.'),
                    TextInput::make('zernio_api_key')->label('Zernio API key')->password()->visible(fn ($get) => $get('provider') === 'zernio')->helperText('Stored encrypted. Leave blank to retain the saved key.'),
                    TextInput::make('zernio_webhook_secret')->label('Zernio webhook signing secret')->password()->visible(fn ($get) => $get('provider') === 'zernio'),
                ]),
                Step::make('Applications')->description('Configure platform-owned integrations')->schema([...$applications, TextInput::make('meta_api_version')->label('Meta Graph API version')->helperText('Use a currently supported version such as the one enabled for your Meta application.'), TextInput::make('linkedin_api_version')->label('LinkedIn API version')->helperText('A currently supported YYYYMM version from your LinkedIn application documentation.')]),
                Step::make('AI assistance')->description('Enable reviewed content creation')->schema([
                    Toggle::make('ai_enabled')->label('Enable AI-assisted posts and ad copy')->live(),
                    TextInput::make('ai_endpoint')->label('Content generation service URL')->url()->visible(fn ($get) => (bool) $get('ai_enabled'))->helperText('HTTPS endpoint implementing the documented content-generation contract.'),
                    TextInput::make('ai_api_key')->label('Content generation API key')->password()->visible(fn ($get) => (bool) $get('ai_enabled')),
                    Placeholder::make('review')->content('Generated content remains a draft. Workspace reviewers approve publishing and campaign activation.'),
                ]),
            ])->submitAction(view('filament.app.pages.setup-wizard-submit')),
        ]);
    }

    public function save(): void
    {
        abort_unless(self::canAccess(), 403);
        $data = $this->form->getState();
        try {
            app(PlatformConfiguration::class)->save(Filament::auth()->user(), $data);
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages(collect($exception->errors())->mapWithKeys(fn ($messages, $key) => ['data.'.$key => $messages])->all());
        } catch (QueryException $exception) {
            report($exception);
            Notification::make()->title('Setup could not be saved')->body('Your form has been kept. Please try again.')->danger()->send();

            return;
        }
        $this->saved = true;
        $this->form->fill(['provider' => $data['provider'], 'ai_enabled' => $data['ai_enabled']]);
        Notification::make()->title('Platform configuration saved')->body('Workspace users can now connect accounts using the selected provider.')->success()->send();
    }
}
