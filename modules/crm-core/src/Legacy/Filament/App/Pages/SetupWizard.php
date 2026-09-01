<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\OAuthConfiguration;
use App\Models\Team;
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
use Illuminate\Support\Facades\DB;
use Throwable;

class SetupWizard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings & integrations';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Setup wizard';

    protected static ?string $title = 'CRM setup wizard';

    protected string $view = 'filament.app.pages.setup-wizard';

    /** @var array<string, mixed> */
    public array $data = [];

    public bool $saved = false;

    public function mount(): void
    {
        $team = Filament::getTenant();
        $facebook = OAuthConfiguration::getConfig('facebook');
        $google = OAuthConfiguration::getConfig('google');
        $linkedin = OAuthConfiguration::getConfig('linkedin');
        $twitter = OAuthConfiguration::getConfig('twitter');
        $microsoft = OAuthConfiguration::getConfig('microsoft');
        $twilio = OAuthConfiguration::getConfig('twilio');
        $whatsapp = OAuthConfiguration::getConfig('whatsapp');
        $helpdesk = OAuthConfiguration::getConfig('helpdesk');
        $zernio = OAuthConfiguration::getConfig('zernio');
        $mailchimp = OAuthConfiguration::getConfig('mailchimp');
        $this->data = [
            'team_name' => $team instanceof Team ? $team->name : '',
            'setup_social' => $facebook !== null || $google !== null || $linkedin !== null || $twitter !== null,
            'setup_voip' => $twilio !== null,
            'setup_whatsapp' => $whatsapp !== null,
            'setup_email' => $google !== null || $microsoft !== null,
            'setup_helpdesk' => $helpdesk !== null,
            'facebook_client_id' => $facebook?->client_id,
            'google_client_id' => $google?->client_id,
            'linkedin_client_id' => $linkedin?->client_id,
            'twitter_client_id' => $twitter?->client_id,
            'zernio_api_key' => $zernio?->client_secret,
            'mailchimp_api_key' => $mailchimp?->client_secret,
            'mailchimp_server_prefix' => $mailchimp?->client_id,
            'email_provider' => $microsoft !== null && $google === null ? 'microsoft' : 'google',
            'email_client_id' => ($microsoft ?? $google)?->client_id,
            'twilio_sid' => $twilio?->client_id,
            'twilio_phone_number' => $twilio?->additional_settings['phone_number'] ?? null,
            'whatsapp_api_url' => $whatsapp?->additional_settings['api_url'] ?? null,
            'whatsapp_phone_number_id' => $whatsapp?->additional_settings['phone_number_id'] ?? null,
            'imap_host' => $helpdesk?->additional_settings['host'] ?? null,
            'imap_username' => $helpdesk?->client_id,
            'smtp_host' => $helpdesk?->additional_settings['smtp_host'] ?? null,
            'setup_team' => true,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Welcome')
                        ->description('Get your workspace ready')
                        ->schema([
                            Section::make('A quick start for your team')
                                ->description('Set the workspace identity first, then add only the services you plan to use. You can return here or use Settings & integrations at any time.')
                                ->icon('heroicon-o-sparkles'),
                            Placeholder::make('security_note')
                                ->label('Credential security')
                                ->content('Secrets are encrypted before they are stored. OAuth consent and connected accounts are completed after this wizard.'),
                        ]),
                    Step::make('Team')
                        ->description('Name your workspace')
                        ->schema([
                            TextInput::make('team_name')->label('Team name')->required()->maxLength(255),
                        ]),
                    Step::make('Social media')
                        ->description('Connect publishing and advertising accounts')
                        ->schema([
                            Toggle::make('setup_social')->label('Configure social media'),
                            Section::make('OAuth applications')->schema([
                                self::credential('facebook', 'Facebook app ID', 'Facebook app secret'),
                                self::credential('google', 'Google client ID', 'Google client secret'),
                                self::credential('linkedin', 'LinkedIn client ID', 'LinkedIn client secret'),
                                self::credential('twitter', 'X/Twitter client ID', 'X/Twitter client secret'),
                            ])->columns(2)->visible(fn (callable $get): bool => (bool) $get('setup_social')),
                            Section::make('Publishing and marketing APIs')->schema([
                                TextInput::make('zernio_api_key')->label('Zernio API key')->password(),
                                TextInput::make('mailchimp_api_key')->label('Mailchimp API key')->password(),
                                TextInput::make('mailchimp_server_prefix')->label('Mailchimp server prefix')->placeholder('us21'),
                            ])->columns(2)->visible(fn (callable $get): bool => (bool) $get('setup_social')),
                        ]),
                    Step::make('Calling')
                        ->description('Enable VoIP and SMS')
                        ->schema([
                            Toggle::make('setup_voip')->label('Configure Twilio VoIP / SMS'),
                            TextInput::make('twilio_sid')->label('Twilio Account SID')->visible(fn (callable $get): bool => (bool) $get('setup_voip')),
                            TextInput::make('twilio_auth_token')->label('Twilio auth token')->password()->visible(fn (callable $get): bool => (bool) $get('setup_voip')),
                            TextInput::make('twilio_phone_number')->label('Twilio phone number')->tel()->visible(fn (callable $get): bool => (bool) $get('setup_voip')),
                        ])->columns(2),
                    Step::make('WhatsApp')
                        ->description('Connect WhatsApp Business Cloud API')
                        ->schema([
                            Toggle::make('setup_whatsapp')->label('Configure WhatsApp Business'),
                            TextInput::make('whatsapp_api_url')->label('API URL')->url()->visible(fn (callable $get): bool => (bool) $get('setup_whatsapp')),
                            TextInput::make('whatsapp_access_token')->label('Access token')->password()->visible(fn (callable $get): bool => (bool) $get('setup_whatsapp')),
                            TextInput::make('whatsapp_phone_number_id')->label('Phone number ID')->visible(fn (callable $get): bool => (bool) $get('setup_whatsapp')),
                        ])->columns(2),
                    Step::make('Email & helpdesk')
                        ->description('Connect inboxes and support channels')
                        ->schema([
                            Toggle::make('setup_email')->label('Configure Gmail or Microsoft 365 OAuth'),
                            Select::make('email_provider')->label('OAuth provider')->options(['google' => 'Google Workspace', 'microsoft' => 'Microsoft 365'])->default('google')->visible(fn (callable $get): bool => (bool) $get('setup_email')),
                            TextInput::make('email_client_id')->label('Email OAuth client ID')->visible(fn (callable $get): bool => (bool) $get('setup_email')),
                            TextInput::make('email_client_secret')->label('Email OAuth client secret')->password()->visible(fn (callable $get): bool => (bool) $get('setup_email')),
                            Toggle::make('setup_helpdesk')->label('Configure IMAP / SMTP helpdesk inbox'),
                            TextInput::make('imap_host')->label('IMAP host')->visible(fn (callable $get): bool => (bool) $get('setup_helpdesk')),
                            TextInput::make('imap_username')->label('IMAP username')->visible(fn (callable $get): bool => (bool) $get('setup_helpdesk')),
                            TextInput::make('imap_password')->label('IMAP password')->password()->visible(fn (callable $get): bool => (bool) $get('setup_helpdesk')),
                            TextInput::make('smtp_host')->label('SMTP host')->visible(fn (callable $get): bool => (bool) $get('setup_helpdesk')),
                        ])->columns(2),
                ])->submitAction(view('filament.app.pages.setup-wizard-submit')),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->data;
        $this->validateSetup($data);

        try {
            DB::transaction(function () use ($data): void {
                $team = Filament::getTenant();
                if ($team instanceof Team) {
                    $team->update(['name' => $data['team_name']]);
                }

                if ($data['setup_social']) {
                    foreach (['facebook', 'google', 'linkedin', 'twitter'] as $provider) {
                        $this->saveOAuth($provider, (string) ($data[$provider.'_client_id'] ?? ''), $data[$provider.'_client_secret'] ?? null);
                    }
                    if (filled($data['zernio_api_key'] ?? null)) {
                        $this->saveOAuth('zernio', 'api', $data['zernio_api_key']);
                    }
                    if (filled($data['mailchimp_api_key'] ?? null) || filled($data['mailchimp_server_prefix'] ?? null)) {
                        $this->saveOAuth('mailchimp', (string) ($data['mailchimp_server_prefix'] ?? ''), $data['mailchimp_api_key'] ?? null);
                    }
                }

                if ($data['setup_voip']) {
                    $this->saveOAuth('twilio', $data['twilio_sid'], $data['twilio_auth_token'] ?? null, ['phone_number' => $data['twilio_phone_number']]);
                }

                if ($data['setup_whatsapp']) {
                    $this->saveOAuth('whatsapp', $data['whatsapp_phone_number_id'] ?? 'business', $data['whatsapp_access_token'] ?? null, [
                        'api_url' => $data['whatsapp_api_url'],
                        'phone_number_id' => $data['whatsapp_phone_number_id'],
                    ]);
                }

                if ($data['setup_email']) {
                    $provider = strtolower((string) ($data['email_provider'] ?? 'google'));
                    $this->saveOAuth($provider, $data['email_client_id'], $data['email_client_secret'] ?? null);
                }

                if ($data['setup_helpdesk']) {
                    $this->saveOAuth('helpdesk', $data['imap_username'], $data['imap_password'] ?? null, [
                        'host' => $data['imap_host'],
                        'username' => $data['imap_username'],
                        'smtp_host' => $data['smtp_host'],
                    ]);
                }
            });

            $this->saved = true;
            Notification::make()->title('CRM setup saved')->body('Your credentials are encrypted at rest. Complete each provider’s OAuth consent step before syncing data.')->success()->send();
        } catch (Throwable) {
            Notification::make()->title('Setup could not be saved')->body('No changes were kept. Check the form and try again.')->danger()->send();
        }
    }

    private static function credential(string $provider, string $idLabel, string $secretLabel): Section
    {
        return Section::make(ucfirst($provider))->schema([
            TextInput::make($provider.'_client_id')->label($idLabel),
            TextInput::make($provider.'_client_secret')->label($secretLabel)->password(),
        ])->columns(2);
    }

    /** @param array<string, mixed> $data */
    private function validateSetup(array $data): void
    {
        $rules = [];
        foreach (['facebook', 'google', 'linkedin', 'twitter'] as $provider) {
            if ($data['setup_social']) {
                $rules[$provider.'_client_id'] = ['required', 'string'];
                if (! $this->hasStoredConfiguration($provider)) {
                    $rules[$provider.'_client_secret'] = ['required', 'string'];
                }
            }
        }
        if ($data['setup_voip']) {
            $rules += ['twilio_sid' => ['required', 'string'], 'twilio_phone_number' => ['required', 'string']];
            if (! $this->hasStoredConfiguration('twilio')) {
                $rules['twilio_auth_token'] = ['required', 'string'];
            }
        }
        if ($data['setup_whatsapp']) {
            $rules['whatsapp_api_url'] = ['required', 'url'];
            if (! $this->hasStoredConfiguration('whatsapp')) {
                $rules['whatsapp_access_token'] = ['required', 'string'];
            }
        }
        if ($data['setup_email']) {
            $rules += ['email_provider' => ['required', 'in:google,microsoft'], 'email_client_id' => ['required', 'string']];
            if (! $this->hasStoredConfiguration((string) ($data['email_provider'] ?? 'google'))) {
                $rules['email_client_secret'] = ['required', 'string'];
            }
        }
        if ($data['setup_helpdesk']) {
            $rules += ['imap_host' => ['required', 'string'], 'imap_username' => ['required', 'string'], 'smtp_host' => ['required', 'string']];
            if (! $this->hasStoredConfiguration('helpdesk')) {
                $rules['imap_password'] = ['required', 'string'];
            }
        }
        $this->validate($rules);
    }

    /** @param array<string, mixed> $settings */
    private function saveOAuth(string $service, string $clientId, ?string $secret, array $settings = []): void
    {
        $existing = OAuthConfiguration::getConfig($service);
        OAuthConfiguration::query()->updateOrCreate(
            ['service_name' => $service],
            [
                'user_id' => Filament::auth()->id(),
                'client_id' => $clientId,
                'client_secret' => filled($secret) ? $secret : $existing?->client_secret,
                'additional_settings' => array_replace($existing?->additional_settings ?? [], $settings),
                'is_active' => true,
            ],
        );
    }

    private function hasStoredConfiguration(string $service): bool
    {
        return OAuthConfiguration::getConfig($service) !== null;
    }
}
