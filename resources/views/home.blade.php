@extends('layouts.app')

@section('content')
<div class="crm-home">
    <section class="crm-hero" aria-labelledby="hero-heading">
        <div class="crm-container crm-hero-grid">
            <div class="crm-hero-copy">
                <p class="crm-eyebrow">The clear workspace for customer-led teams</p>
                <h1 id="hero-heading">Turn every conversation into your next opportunity.</h1>
                <p class="crm-hero-lead">Liberu CRM brings contacts, sales, conversations, service, and reporting together so your team always knows what matters next.</p>
                <div class="crm-hero-actions">
                    <a href="{{ route('register') }}" class="crm-button crm-button-primary">{{ config('saas.enabled') ? 'Start your free trial' : 'Get started free' }} <span aria-hidden="true">→</span></a>
                    <a href="#features" class="crm-button crm-button-secondary">Explore the workspace</a>
                </div>
                <p class="crm-hero-note">{{ config('saas.enabled') ? '14 days to see the difference. No long-term commitment.' : 'Self-hosted, open, and ready to make your own.' }}</p>
            </div>
            <div class="crm-dashboard-preview" aria-label="CRM workspace preview">
                <div class="crm-preview-bar"><span class="crm-preview-dot"></span><span class="crm-preview-dot"></span><span class="crm-preview-dot"></span><span class="crm-preview-url">workspace / overview</span></div>
                <div class="crm-preview-body">
                    <div class="crm-preview-sidebar"><span class="crm-preview-logo">L</span><span class="crm-preview-line crm-preview-line-active"></span><span class="crm-preview-line"></span><span class="crm-preview-line"></span><span class="crm-preview-line"></span></div>
                    <div class="crm-preview-content">
                        <div class="crm-preview-heading"><div><span class="crm-preview-kicker">Monday, {{ now()->format('F j') }}</span><strong>Your workspace</strong></div><span class="crm-preview-avatar">JD</span></div>
                        <div class="crm-preview-metrics"><div><span>Open deals</span><strong>£248k</strong><small>+18.4%</small></div><div><span>Active contacts</span><strong>1,284</strong><small>+12.8%</small></div><div><span>Tasks due</span><strong>24</strong><small>8 today</small></div></div>
                        <div class="crm-preview-chart"><div class="crm-preview-chart-head"><strong>Pipeline momentum</strong><span>Last 30 days</span></div><div class="crm-bars"><i style="height: 34%"></i><i style="height: 52%"></i><i style="height: 44%"></i><i style="height: 68%"></i><i style="height: 61%"></i><i style="height: 82%"></i><i style="height: 94%"></i></div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="crm-proof" aria-label="What Liberu CRM brings together">
        <div class="crm-container crm-proof-row"><span>One connected workspace for</span><strong>Sales</strong><strong>Service</strong><strong>Operations</strong><strong>Growth</strong></div>
    </section>

    <section id="features" class="crm-section" aria-labelledby="features-heading">
        <div class="crm-container">
            <div class="crm-section-heading"><p class="crm-eyebrow">Everything in context</p><h2 id="features-heading">The tools your team needs to move from busywork to momentum.</h2><p>Stop switching between disconnected systems. Liberu CRM gives every team a shared view of the customer and a clear path forward.</p></div>
            <div class="crm-feature-grid">
                @foreach([
                    ['icon' => 'users', 'title' => 'Contacts & companies', 'text' => 'Build a complete customer record with people, companies, activity, notes, and custom fields in one place.'],
                    ['icon' => 'chart', 'title' => 'Sales pipeline', 'text' => 'Manage leads, deals, stages, forecasts, and follow-ups with a pipeline your whole team can trust.'],
                    ['icon' => 'message', 'title' => 'Unified conversations', 'text' => 'Bring email, WhatsApp, SMS, and calling into one workflow so every reply has the right context.'],
                    ['icon' => 'check', 'title' => 'Tasks & workflows', 'text' => 'Turn repeatable work into accountable processes with tasks, reminders, automations, and approvals.'],
                    ['icon' => 'support', 'title' => 'Helpdesk & knowledge base', 'text' => 'Resolve customer questions faster with tickets, shared answers, and a searchable knowledge base.'],
                    ['icon' => 'report', 'title' => 'Reports & insights', 'text' => 'See what is working with dashboards, activity analytics, campaign performance, and actionable forecasts.'],
                    ['icon' => 'team', 'title' => 'Teams & permissions', 'text' => 'Give every person the right access with teams, roles, permissions, and secure tenant isolation.'],
                    ['icon' => 'plug', 'title' => 'Integrations & API', 'text' => 'Connect the tools you already use through social sign-in, webhooks, REST APIs, and extensible modules.'],
                ] as $feature)
                    <article class="crm-feature-card"><div class="crm-feature-icon" aria-hidden="true">@include('components.home-feature-icon', ['icon' => $feature['icon']])</div><h3>{{ $feature['title'] }}</h3><p>{{ $feature['text'] }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="crm-workflow-section" aria-labelledby="workflow-heading">
        <div class="crm-container crm-workflow-grid"><div class="crm-workflow-panel"><p class="crm-eyebrow">A better way to work</p><h2 id="workflow-heading">Know what happened. Know what is next.</h2><p>From the first enquiry to renewal, your team sees the full story without hunting through inboxes or spreadsheets.</p><div class="crm-workflow-list"><div><span>01</span><p><strong>Capture</strong><br>Keep every lead and enquiry moving into one reliable system.</p></div><div><span>02</span><p><strong>Coordinate</strong><br>Give sales, service, and operations the same customer context.</p></div><div><span>03</span><p><strong>Grow</strong><br>Use real activity and pipeline data to focus your next best action.</p></div></div></div><div class="crm-quote-card"><span class="crm-quote-mark">“</span><blockquote>A CRM should make the right work obvious. Liberu gives our team the context to act with confidence.</blockquote><div class="crm-quote-person"><span class="crm-preview-avatar">LT</span><span><strong>Built for modern teams</strong><small>Simple enough to adopt. Powerful enough to grow.</small></span></div></div></div>
    </section>

    <section class="crm-final-cta" aria-labelledby="cta-heading"><div class="crm-container"><p class="crm-eyebrow">Ready when you are</p><h2 id="cta-heading">Give your team one clear place to grow.</h2><p>Set up your workspace, invite your team, and start turning customer relationships into lasting momentum.</p><a href="{{ route('register') }}" class="crm-button crm-button-light">{{ config('saas.enabled') ? 'Start your free trial' : 'Create your free workspace' }} <span aria-hidden="true">→</span></a><p class="crm-cta-login">Already have an account? <a href="{{ route('login') }}">Sign in</a></p></div></section>
</div>
@endsection
