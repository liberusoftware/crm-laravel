<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetFilamentDefaultTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Filament::getTenant() === null) {
            Filament::setTenant(Filament::getUserDefaultTenant(Filament::auth()->user()));
        }

        return $next($request);
    }
}
