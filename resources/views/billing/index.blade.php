@extends('layouts.app')

@section('content')
<main class="crm-billing" aria-labelledby="billing-title">
    <section class="crm-billing-hero">
        <div class="crm-container">
            <p class="crm-eyebrow">Plans that grow with your team</p>
            <h1 id="billing-title">Choose the workspace that fits your next stage.</h1>
            <p>Start with the complete CRM toolkit. Upgrade when your team is ready, with no hidden setup fees and every core workflow included.</p>
            <div class="crm-billing-points" aria-label="Plan benefits">
                <span><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 10 4 4 8-8" /></svg>Full CRM workspace</span>
                <span><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 10 4 4 8-8" /></svg>Team collaboration</span>
                <span><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m4 10 4 4 8-8" /></svg>Cancel anytime</span>
            </div>
        </div>
    </section>

    <section class="crm-billing-plans" aria-label="Available plans">
        <div class="crm-container">
            <div class="crm-plan-grid">
                @foreach($plans as $key => $plan)
                    <article class="crm-plan-card {{ $loop->first ? 'crm-plan-card-featured' : '' }}">
                        @if($loop->first)
                            <span class="crm-plan-badge">Most popular</span>
                        @endif
                        <div class="crm-plan-heading"><div><p class="crm-plan-kicker">{{ ucfirst($key) }} workspace</p><h2>{{ ucfirst($key) }}</h2></div><span class="crm-plan-mark" aria-hidden="true">✦</span></div>
                        <p class="crm-plan-description">A focused workspace for teams ready to turn customer context into momentum.</p>
                        <div class="crm-plan-price"><strong>£{{ number_format($plan['amount'], 2) }}</strong><span>/ {{ $plan['interval'] }}</span></div>
                        <ul class="crm-plan-features"><li>Contacts, companies, and custom fields</li><li>Deals, pipeline, tasks, and reporting</li><li>Shared conversations and helpdesk tools</li><li>Teams, permissions, and integrations</li></ul>
                        <form method="post" action="{{ route('billing.subscribe') }}" data-stripe-form>
                            @csrf
                            <input type="hidden" name="plan" value="{{ $key }}">
                            <label class="crm-form-label" for="card-{{ $key }}">Secure card details</label>
                            <div id="card-{{ $key }}" data-stripe-card class="crm-stripe-card"></div>
                            <input type="hidden" name="payment_method_id" data-stripe-payment-method>
                            <button class="crm-button crm-button-primary crm-plan-submit" type="submit">Start {{ config('saas.trial_days') }}-day trial <span aria-hidden="true">→</span></button>
                            <p data-stripe-error class="crm-stripe-error" role="alert"></p>
                        </form>
                        <p class="crm-secure-note"><svg viewBox="0 0 20 20" aria-hidden="true"><rect x="4" y="9" width="12" height="9" rx="2"/><path d="M6.5 9V6a3.5 3.5 0 0 1 7 0v3"/></svg>Payments securely processed by Stripe</p>
                    </article>
                @endforeach
            </div>
            <p class="crm-billing-footnote">Your trial includes every feature. Billing begins automatically after the trial period. <a href="{{ config('app.url') }}">Return to the homepage</a>.</p>
        </div>
    </section>
</main>
@endsection
