@extends('layouts.app')

@section('content')
<main class="crm-container py-12" aria-labelledby="billing-lock-title">
    <section class="crm-card mx-auto max-w-2xl p-8 text-center">
        <h1 id="billing-lock-title" class="text-2xl font-semibold">Payment required</h1>
        <p class="mt-3">Access is paused because the latest subscription payment could not be completed.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a class="crm-button bg-indigo-600 text-white" href="{{ route('billing.index') }}">Update payment details</a>
            <a class="crm-button border border-slate-300" href="{{ route('data.export') }}">Export your data</a>
        </div>
    </section>
</main>
@endsection
