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
        <div class="mt-8 border-t border-slate-200 pt-6">
            <p class="mb-3 text-sm text-slate-600">Need to leave this account for now?</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="crm-button w-full justify-center border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 sm:w-auto">Log out</button>
            </form>
        </div>
    </section>
</main>
@endsection
