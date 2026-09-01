@extends('layouts.app')

@section('content')
<main class="crm-container py-10" aria-labelledby="billing-title">
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Liberu CRM</p>
        <h1 id="billing-title" class="mt-2 text-3xl font-semibold">Choose your plan</h1>
        <p class="mt-2 text-slate-600">Every plan starts with a {{ config('saas.trial_days') }}-day trial. Your card is required up front and billing starts automatically after the trial.</p>
    </div>
    <div class="crm-grid">
        @foreach($plans as $key => $plan)
            <article class="crm-card p-6">
                <h2 class="text-xl font-semibold">{{ ucfirst($key) }}</h2>
                <p class="mt-4 text-3xl font-bold">£{{ number_format($plan['amount'], 2) }} <span class="text-sm font-normal text-slate-500">/{{ $plan['interval'] }}</span></p>
                <p class="mt-3 text-sm text-slate-600">Full CRM workspace access, integrations, and team collaboration.</p>
                <form method="post" action="{{ route('billing.subscribe') }}" data-stripe-form class="mt-6 space-y-3">
                    @csrf
                    <input type="hidden" name="plan" value="{{ $key }}">
                    <label class="block text-sm font-medium" for="card-{{ $key }}">Card details</label>
                    <div id="card-{{ $key }}" data-stripe-card class="rounded-md border border-slate-300 bg-white p-3"></div>
                    <input type="hidden" name="payment_method_id" data-stripe-payment-method>
                    <button class="crm-button w-full bg-indigo-600 text-white" type="submit">Start {{ config('saas.trial_days') }}-day trial</button>
                    <p data-stripe-error class="text-sm text-red-700" role="alert"></p>
                </form>
            </article>
        @endforeach
    </div>
</main>
@endsection
