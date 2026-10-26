<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleCalendarController extends Controller
{
    public function redirectToGoogle(): Redirector|RedirectResponse
    {
        $client = new Google_Client();
        $client->setAuthConfig(config('services.google.credentials_path'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setRedirectUri(route('google.callback'));
        $state = Str::random(64);
        session(['google_calendar_oauth_state' => $state]);
        $client->setState($state);

        return redirect($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $expectedState = $request->session()->pull('google_calendar_oauth_state');
        $state = $request->input('state');
        abort_unless(is_string($expectedState) && $expectedState !== '' && is_string($state) && hash_equals($expectedState, $state), 403);

        $client = new Google_Client();
        $client->setAuthConfig(config('services.google.credentials_path'));
        $client->setRedirectUri(route('google.callback'));

        if ($request->get('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
            $client->setAccessToken($token);

            Auth::user()->update(['google_calendar_token' => json_encode($token)]);

            return redirect()->route('tasks.index')->with('success', 'Google Calendar connected successfully.');
        }

        return redirect()->route('tasks.index')->with('error', 'Failed to connect Google Calendar.');
    }
}
