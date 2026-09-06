<?php

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Liberu\Foundation\Theme\Services\ThemeManager;

it('binds the ThemeManager singleton and the theme alias to one instance', function () {
    expect(app(ThemeManager::class))->toBeInstanceOf(ThemeManager::class)
        ->and(app('theme'))->toBe(app(ThemeManager::class));
});

it('registers the theme blade directives', function () {
    expect(Blade::getCustomDirectives())
        ->toHaveKeys(['themeAsset', 'themeCss', 'themeJs', 'themeLayout']);
});

it('renders the themeAsset directive against the active theme', function () {
    expect(Blade::render("@themeAsset('resources/css/app.css')"))
        ->toContain('themes/'.active_theme().'/resources/css/app.css');
});

it('resolves a shared view name through the active theme inheritance chain', function () {
    app(ThemeManager::class)->setTheme('dark');
    expect(View::getFinder()->find('layouts.app'))
        ->toContain('themes/base/resources/views/layouts/app.blade.php');

    View::getFinder()->flush();

    app(ThemeManager::class)->setTheme('default');
    expect(View::getFinder()->find('layouts.app'))
        ->toContain('themes/base/resources/views/layouts/app.blade.php');
});

it('does not throw rendering themeCss/themeJs when the theme asset is not in the Vite manifest', function () {
    app(ThemeManager::class)->setTheme('dark');

    expect(Blade::render('@themeCss @themeJs'))->toBeString();
});

it('renders theme assets while the Vite development server is running', function () {
    $hotFile = public_path('hot');
    $backup = File::exists($hotFile) ? File::get($hotFile) : null;
    File::put($hotFile, 'http://localhost:5173');

    try {
        app(ThemeManager::class)->setTheme('theme-crm');

        expect(Blade::render("@php app('theme')->selectForSurface('portal') @endphp @themeVite"))
            ->toContain('@vite/client')
            ->toContain('themes/theme-crm/resources/css/app.css')
            ->toContain('themes/theme-crm/resources/js/app.js');
    } finally {
        if ($backup === null) {
            File::delete($hotFile);
        } else {
            File::put($hotFile, $backup);
        }
    }
});

it('persists set_theme to session and the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    set_theme('dark');

    expect(session('theme_preference'))->toBe('dark')
        ->and($user->fresh()->theme_preference)->toBe('dark');
});

it('set_theme without auth writes session only and does not throw', function () {
    set_theme('dark');

    expect(session('theme_preference'))->toBe('dark');
});
