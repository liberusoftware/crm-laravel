<?php

namespace App\Filament\App\Pages;

use App\Models\OAuthConfiguration;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Config;

class TwilioSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected string $view = 'filament.app.pages.twilio-settings';

    public ?string $sid = null;

    public ?string $auth_token = null;

    public ?string $phone_number = null;

    public function mount(): void
    {
        $configuration = OAuthConfiguration::getConfig('twilio');
        $this->sid = $configuration?->client_id ?? config('services.twilio.sid');
        $this->phone_number = $configuration?->additional_settings['phone_number'] ?? config('services.twilio.phone_number');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sid')
                    ->label('Twilio SID')
                    ->required(),
                TextInput::make('auth_token')
                    ->label('Twilio Auth Token')
                    ->password()
                    ->required(),
                TextInput::make('phone_number')
                    ->label('Twilio Phone Number')
                    ->tel()
                    ->required(),
            ]);
    }

    public function submit(): void
    {
        $this->validate();

        OAuthConfiguration::query()->updateOrCreate(
            ['service_name' => 'twilio'],
            [
                'user_id' => auth()->id(),
                'client_id' => $this->sid,
                'client_secret' => $this->auth_token,
                'additional_settings' => ['phone_number' => $this->phone_number],
                'is_active' => true,
            ],
        );

        Config::set('services.twilio.sid', $this->sid);
        Config::set('services.twilio.auth_token', $this->auth_token);
        Config::set('services.twilio.phone_number', $this->phone_number);

        Notification::make()
            ->title('Twilio settings updated successfully')
            ->success()
            ->send();
    }
}
