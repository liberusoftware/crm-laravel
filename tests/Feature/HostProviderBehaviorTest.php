<?php

use App\Console\Kernel;
use App\Exceptions\Handler;
use App\Filament\App\Resources\ContactResource;
use App\Filament\CrmNavigation;
use App\Models\OAuthConfiguration;
use App\Models\User;
use App\Providers\BroadcastServiceProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\FilamentServiceProvider;
use App\Providers\SocialstreamServiceProvider;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Tests\Fixtures\HostNavigation\Neutral;
use Tests\Fixtures\HostNavigation\Search;
use Tests\Fixtures\HostNavigation\Ticket;

it('schedules publishing and analytics commands and loads console routes', function () {
    $kernel = new Kernel($this->app, $this->app['events']);
    $events = $kernel->resolveConsoleSchedule()->events();
    expect($events)->toHaveCount(2)
        ->and($events[0]->command)->toContain('social-media:publish-scheduled')
        ->and($events[0]->expression)->toBe('* * * * *')
        ->and($events[1]->command)->toContain('social-media:update-analytics')
        ->and($events[1]->expression)->toBe('0 * * * *');

    $configuredKernel = $this->app->make(ConsoleKernel::class);
    expect($configuredKernel->all())->toHaveKey('inspire')
        ->and($configuredKernel->all()['db:seed']->getDefinition()->hasOption('tenants'))->toBeTrue();
    Artisan::swap($kernel);
    try {
        expect($kernel->all())->toHaveKey('inspire');
        $this->artisan('inspire')->assertExitCode(0);
    } finally {
        Artisan::swap($configuredKernel);
    }
});

it('reports application exceptions through its registered callback', function () {
    $path = storage_path('logs/host-handler-test.log');
    config(['logging.default' => 'single', 'logging.channels.single.path' => $path]);
    $this->app['log']->forgetChannel('single');
    $handler = new Handler($this->app);
    $handler->register();
    $error = new RuntimeException('Host exception reporting regression');
    expect($handler->shouldReport($error))->toBeTrue();
    $handler->report($error);
    expect(File::get($path))->toContain($error->getMessage());
    File::delete($path);
});

it('registers broadcast authorization and the legacy application panel', function () {
    (new BroadcastServiceProvider($this->app))->boot();
    $route = $this->app['router']->getRoutes()->match(Request::create('/broadcasting/auth', 'POST'));
    expect($route->uri())->toBe('broadcasting/auth');
    $panel = (new FilamentServiceProvider($this->app))->panel(Panel::make());
    expect($panel->getId())->toBe('app')->and($panel->getPath())->toBe('app')
        ->and($panel->getResources())->toContain(ContactResource::class);
});

it('groups support, intelligence and uncategorized resources without overriding existing groups', function () {
    $panel = Panel::make()->id('host-navigation')->resources([stdClass::class, Ticket::class, Search::class, Neutral::class]);
    (new CrmNavigation())->configure($panel);
    expect(Ticket::getNavigationGroup())->toBe('Support')
        ->and(Search::getNavigationGroup())->toBe('Data & intelligence')
        ->and(Neutral::getNavigationGroup())->toBe('CRM');
    Neutral::navigationGroup('Custom');
    (new CrmNavigation())->configure($panel);
    expect(Neutral::getNavigationGroup())->toBe('Custom');
});

it('normalizes login throttling and scopes second-factor limits by login session', function () {
    $request = Request::create('/login', 'POST', ['email' => 'Tést@Example.COM']);
    $request->setLaravelSession($this->app['session.store']);
    $request->session()->put('login.id', 42);
    $login = RateLimiter::limiter('login')($request);
    $twoFactor = RateLimiter::limiter('two-factor')($request);
    expect($login->maxAttempts)->toBe(5)->and($login->decaySeconds)->toBe(60)
        ->and($login->key)->toBe('test@example.com|127.0.0.1')
        ->and($twoFactor->key)->toBe(42)->and($twoFactor->maxAttempts)->toBe(5);
});

it('constructs configured OAuth drivers, preserves posting scopes and requests offline Google access', function () {
    $user = User::factory()->create();
    $request = $this->app['request'];
    $request->setLaravelSession($this->app['session.store']);
    foreach (['facebook', 'google', 'youtube', 'linkedin', 'twitter', 'instagram', 'twilio', 'unsupported'] as $service) {
        OAuthConfiguration::query()->create(['user_id' => $user->id, 'service_name' => $service, 'client_id' => 'id-'.$service, 'client_secret' => 'secret-'.$service, 'is_active' => true]);
    }
    Socialite::forgetDrivers();
    $provider = new class($this->app) extends SocialstreamServiceProvider
    {
        public function providerClass(string $name): string
        {
            return $this->getSocialiteProviderClass($name);
        }
    };
    $provider->boot();
    foreach (['facebook', 'google', 'youtube', 'linkedin', 'twitter', 'instagram', 'twilio'] as $service) {
        $driver = Socialite::driver($service);
        parse_str(parse_url($driver->redirect()->getTargetUrl(), PHP_URL_QUERY), $query);
        expect($query['client_id'])->toBe('id-'.$service)
            ->and($query['redirect_uri'])->toBe(config('app.url').'/oauth/'.$service.'/callback');
        if (in_array($service, ['google', 'youtube'], true)) {
            expect($query['access_type'])->toBe('offline')->and($query['prompt'])->toBe('consent');
        }
    }
    foreach (['facebook', 'google', 'linkedin-openid', 'twitter-oauth-2', 'github', 'gitlab', 'bitbucket'] as $driver) {
        expect(is_subclass_of($provider->providerClass($driver), AbstractProvider::class))->toBeTrue();
    }
    expect(fn () => $provider->providerClass('unsupported'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Socialite::driver('unsupported'))->toThrow(InvalidArgumentException::class);
});

it('builds tenant-scoped profile, setup and team menu URLs only for verified tenant members', function () {
    $user = User::factory()->withPersonalTeam()->create(['email_verified_at' => now()]);
    $this->actingAs($user);
    $panel = Filament::getPanel('app');
    Filament::setCurrentPanel($panel);
    Filament::setTenant($user->currentTeam);
    $provider = new AppPanelProvider($this->app);
    expect($provider->shouldRegisterMenuItem())->toBeTrue();
    $items = collect($panel->getUserMenuItems())->filter(fn ($item) => in_array($item->getLabel(), ['Profile', 'Setup wizard', 'Team Settings'], true));
    expect($items)->toHaveCount(3);
    foreach ($items as $item) {
        expect($item->getUrl())->toContain('/app/'.$user->current_team_id.'/');
    }
    Filament::setTenant(null);
    expect($provider->shouldRegisterMenuItem())->toBeFalse();
});

it('records logout audit events for the authenticated actor', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user);
    event(new Logout('web', $user));
    $this->assertDatabaseHas('audit_logs', ['user_id' => $user->id, 'action' => 'logout', 'description' => 'User logged out']);
});
