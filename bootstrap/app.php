<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureBillingAccess;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetPermissionsTeamContext;
use App\Http\Middleware\SetTenantContext;
use App\Http\Middleware\VerifyTwilioRequest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Liberu\Foundation\Localization\Http\Middleware\SetLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/socialstream.php'));
        },
    )
    ->withCommands([
        ...glob(__DIR__.'/../modules/*/src/Legacy/Console/Commands') ?: [],
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'twilio.verify' => VerifyTwilioRequest::class,
            'billing.access' => EnsureBillingAccess::class,
        ]);

        $middleware->trimStrings(except: [
            'current_password',
            'password',
            'password_confirmation',
        ]);

        // The SAML ACS is a cross-site POST from the IdP with no CSRF token; the
        // signed assertion + InResponseTo (bound to our session) protect it.
        $middleware->validateCsrfTokens(except: [
            'saml/*/acs',
        ]);

        $middleware->web(append: [
            SecurityHeaders::class,
            SetLocale::class,
            // Scope Spatie permissions to the user's current team so per-team
            // roles resolve. Appended so the session guard is available.
            SetPermissionsTeamContext::class,
            EnsureBillingAccess::class,
        ]);

        // Establish the tenant from the Sanctum user before route-model
        // binding runs, so the IsTenantModel global scope filters API queries.
        // SetPermissionsTeamContext does the same for per-team role resolution.
        $middleware->api(prepend: [
            SetTenantContext::class,
            SetPermissionsTeamContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
