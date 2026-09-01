<x-guest-layout>
    <x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    <x-validation-errors class="mb-4" />

    <div class="mb-6">
        <p class="text-xl font-semibold tracking-tight">{{ __('Create your account') }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Set up your workspace in a few simple steps.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-label for="name" value="{{ __('Full name') }}" class="cs-label" />
            <x-input id="name" class="cs-input mt-1 block" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Jane Smith" />
        </div>

        <div class="mt-4">
            <x-label for="email" value="{{ __('Email address') }}" class="cs-label" />
            <x-input id="email" class="cs-input mt-1 block" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
        </div>

        <div class="mt-4">
            <x-label for="password" value="{{ __('Password') }}" class="cs-label" />
            <x-input id="password" class="cs-input mt-1 block" type="password" name="password" required autocomplete="new-password" />
        </div>

        <div class="mt-4">
            <x-label for="password_confirmation" value="{{ __('Confirm password') }}" class="cs-label" />
            <x-input id="password_confirmation" class="cs-input mt-1 block" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mt-4">
                <x-label for="terms">
                    <div class="flex items-center">
                        <x-checkbox name="terms" id="terms" required />
                        <div class="ms-2">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Terms of Service').'</a>',
                                'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </div>
                    </div>
                </x-label>
            </div>
        @endif

        <div class="mt-6 flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
               href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-button class="cs-btn cs-btn--primary w-full sm:w-auto">
                {{ __('Register') }}
            </x-button>
        </div>
    </form>

    @if (JoelButcher\Socialstream\Socialstream::show())
        <x-socialstream />
    @endif
    </x-authentication-card>
</x-guest-layout>
