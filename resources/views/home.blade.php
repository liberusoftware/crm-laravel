@extends('layouts.app')

@section('content')
<div class="crm-container py-8">
    @if(config('saas.enabled'))
        <section class="crm-card mb-8 overflow-hidden bg-slate-950 px-6 py-14 text-white sm:px-12" aria-labelledby="welcome-heading">
            <p class="mb-3 text-sm font-semibold uppercase tracking-[0.2em] text-indigo-300">Liberu CRM for growing teams</p>
            <h1 id="welcome-heading" class="max-w-4xl text-4xl font-bold tracking-tight sm:text-6xl">Every relationship. One clear workspace.</h1>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-300">Bring contacts, pipeline, conversations, email, WhatsApp, and calling together so your team can move faster and give every customer a better experience.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="crm-button inline-flex items-center bg-indigo-500 text-white">Start your 14-day trial</a>
                <a href="{{ route('login') }}" class="crm-button inline-flex items-center border border-slate-600 text-white">Sign in</a>
            </div>
            <p class="mt-5 text-sm text-slate-400">Card required for uninterrupted access. Choose £19.99 monthly or £199.99 yearly after your trial.</p>
        </section>
        <section class="mb-8" aria-labelledby="saas-benefits-heading">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div><p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Built for momentum</p><h2 id="saas-benefits-heading" class="mt-1 text-2xl font-semibold">The tools your team needs, connected</h2></div>
                <a href="{{ route('billing.index') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">View plans →</a>
            </div>
            <div class="crm-grid">
                @foreach([
                    ['title' => 'One customer view', 'text' => 'Keep context, activity, tasks, and deals together from first touch to renewal.'],
                    ['title' => 'Conversations that convert', 'text' => 'Coordinate email, WhatsApp, SMS, and Twilio calling from one reliable workflow.'],
                    ['title' => 'Ready for your team', 'text' => 'Use roles, approvals, automations, reporting, and integrations without stitching tools together.'],
                ] as $benefit)
                    <article class="crm-card p-6"><h3 class="text-lg font-semibold">{{ $benefit['title'] }}</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ $benefit['text'] }}</p></article>
                @endforeach
            </div>
        </section>
    @else
        <section class="crm-card mb-8 bg-slate-950 px-6 py-12 text-white sm:px-10" aria-labelledby="welcome-heading">
            <p class="mb-3 text-sm font-semibold uppercase tracking-[0.2em] text-teal-300">Liberu CRM · Free self-hosted edition</p>
            <h1 id="welcome-heading" class="max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl">A foundation you can build on.</h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-300">Run a capable CRM on your own infrastructure with customer relationships, sales activity, team operations, and integrations in one Laravel application.</p>
            <div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('register') }}" class="crm-button inline-flex items-center bg-teal-400 text-slate-950">Get started free</a><a href="{{ route('login') }}" class="crm-button inline-flex items-center border border-slate-600 text-white">Sign in</a></div>
            <p class="mt-5 text-sm text-slate-400">Free mode is enabled for local and self-hosted deployments. No payment details are required.</p>
        </section>
    @endif

    @auth
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                {{ session('success') }}
            </div>
        @endif
    @endauth

    @auth
        <div class="crm-card mb-6 p-6"><h2 class="text-2xl font-bold">Welcome back to {{ \App\Helpers\SiteSettingsHelper::get('name') }}</h2><p class="mt-2 text-slate-600">Manage your account and access your dashboard.</p></div>
    @endauth

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @auth
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Submit a Ticket</h2>
                <x-validation-errors class="mb-4" />
                <form action="{{ route('tickets.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <x-label for="subject" value="Subject" />
                        <x-input id="subject" class="block mt-1 w-full" type="text" name="subject" required />
                        <x-input-error for="subject" class="mt-1" />
                    </div>
                    <div class="mb-4">
                        <x-label for="body" value="Description" />
                        <textarea id="body" name="body" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                        <x-input-error for="body" class="mt-1" />
                    </div>
                    <x-button class="bg-green-800 hover:bg-green-700 active:bg-green-900 focus:border-green-900 ring-green-300">
                        Submit Ticket
                    </x-button>
                </form>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Submit a Ticket</h2>
                <p class="text-gray-600 dark:text-gray-400">
                    Please
                    <a href="{{ Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/admin/login') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                        log in
                    </a>
                    to submit a ticket.
                </p>
            </div>
        @endauth

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Knowledge Base</h2>
            @if($knowledgeBaseArticles->isNotEmpty())
                <ul class="space-y-2">
                    @foreach($knowledgeBaseArticles as $article)
                        <li>
                            <a href="{{ route('knowledge-base.show', $article) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                {{ $article->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">No articles yet.</p>
            @endif
        </div>
    </div>

    @auth
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request a Quote</h2>
            <x-validation-errors class="mb-4" />
            <form action="{{ route('quote-requests.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-label for="name" value="Name" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" required />
                        <x-input-error for="name" class="mt-1" />
                    </div>
                    <div>
                        <x-label for="email" value="Email" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" required />
                        <x-input-error for="email" class="mt-1" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-label for="message" value="Message" />
                    <textarea id="message" name="message" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                    <x-input-error for="message" class="mt-1" />
                </div>
                <div class="mt-4">
                    <x-button class="bg-green-800 hover:bg-green-700 active:bg-green-900 focus:border-green-900 ring-green-300">
                        Request Quote
                    </x-button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request a Quote</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Please
                <a href="{{ Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/admin/login') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                    log in
                </a>
                to request a quote.
            </p>
        </div>
    @endauth

    @unless(config('saas.enabled'))
        <p class="mt-8 text-center text-sm text-slate-500">Clear Signal gives this self-hosted CRM its focused, accessible foundation. <a class="underline hover:text-slate-700" href="https://github.com/liberusoftware/boilerplate-laravel">Explore the foundation</a>.</p>
    @endunless
</div>
@endsection
