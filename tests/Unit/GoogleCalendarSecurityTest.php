<?php

namespace Tests\Unit;

use App\Http\Controllers\GoogleCalendarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class GoogleCalendarSecurityTest extends TestCase
{
    public function test_authorization_generates_state_and_matching_callback_consumes_it(): void
    {
        Route::get('/test-google/callback', fn () => null)->name('google.callback');
        Route::get('/test-tasks', fn () => null)->name('tasks.index');
        Route::getRoutes()->refreshNameLookups();
        config(['services.google.credentials_path' => ['web' => [
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
            'redirect_uris' => ['http://localhost/google/callback'],
        ]]]);
        $session = $this->app['session']->driver();
        $response = app(GoogleCalendarController::class)->redirectToGoogle();
        parse_str(parse_url($response->getTargetUrl(), PHP_URL_QUERY), $parameters);
        $state = $session->get('google_calendar_oauth_state');
        $this->assertIsString($state);
        $this->assertSame(64, strlen($state));
        $this->assertSame($state, $parameters['state']);

        $request = Request::create('/google/callback', 'GET', ['state' => $state]);
        $request->setLaravelSession($session);
        app(GoogleCalendarController::class)->handleGoogleCallback($request);
        $this->assertFalse($session->has('google_calendar_oauth_state'));

        $this->expectException(HttpException::class);
        app(GoogleCalendarController::class)->handleGoogleCallback($request);
    }

    public function test_callback_rejects_missing_mismatched_and_array_state_before_contacting_google(): void
    {
        foreach ([null, 'wrong-state', ['expected-state']] as $state) {
            $session = $this->app['session']->driver();
            $session->put('google_calendar_oauth_state', 'expected-state');
            $request = Request::create('/google/callback', 'GET', ['state' => $state, 'code' => 'untrusted-code']);
            $request->setLaravelSession($session);

            try {
                app(GoogleCalendarController::class)->handleGoogleCallback($request);
                $this->fail('Untrusted OAuth state was accepted.');
            } catch (HttpException $exception) {
                $this->assertSame(403, $exception->getStatusCode());
                $this->assertFalse($session->has('google_calendar_oauth_state'));
            }
        }
    }

    public function test_callback_rejects_a_state_without_a_pending_authorization(): void
    {
        $request = Request::create('/google/callback', 'GET', ['state' => 'attacker-state']);
        $request->setLaravelSession($this->app['session']->driver());
        $this->expectException(HttpException::class);

        app(GoogleCalendarController::class)->handleGoogleCallback($request);
    }
}
