<x-guest-layout>
    <x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    <x-validation-errors class="mb-4" />

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-6">
        <p class="text-xl font-semibold tracking-tight">{{ __('Welcome back') }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Sign in to continue to your workspace.') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-label for="email" value="{{ __('Email address') }}" class="cs-label" />
            <x-input id="email" class="cs-input mt-1 block" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
        </div>

        <div class="mt-4">
            <x-label for="password" value="{{ __('Password') }}" class="cs-label" />
            <x-input id="password" class="cs-input mt-1 block" type="password" name="password" required autocomplete="current-password" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="flex items-center">
                <x-checkbox id="remember_me" name="remember" />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                   href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-button class="cs-btn cs-btn--primary w-full sm:w-auto">
                {{ __('Log in') }}
            </x-button>
        </div>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 border-t border-[var(--border)] pt-5 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('New to :app?', ['app' => config('app.name')]) }}
            <a href="{{ route('register') }}" class="ms-1 font-semibold">{{ __('Create an account') }}</a>
        </p>
    @endif

    @if (JoelButcher\Socialstream\Socialstream::show())
        <x-socialstream />
    @endif
    </x-authentication-card>
</x-guest-layout>
