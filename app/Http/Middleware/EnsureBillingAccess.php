<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Team;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureBillingAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('saas.enabled', false) || ! $request->user()) {
            return $next($request);
        }

        $user = $request->user();
        if (! $user instanceof User || ! $user->currentTeam instanceof Team) {
            return $next($request);
        }
        $team = $user->currentTeam;
        if ($team->hasActiveSubscription()) {
            return $next($request);
        }

        $allowed = $request->routeIs('billing.*') || $request->routeIs('data.export');
        if ($allowed) {
            return $next($request);
        }

        return response()->view('billing.locked', ['team' => $team], 402);
    }
}
