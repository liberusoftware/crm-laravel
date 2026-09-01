@extends('layouts.app')

@section('content')
<div class="crm-container py-8">
    <div class="crm-card overflow-hidden">
        @foreach ($contacts as $contact)
            <div class="border-b border-slate-200 px-4 py-3 last:border-b-0">
                <div class="font-medium">{{ $contact->name }}</div>
                <div class="text-sm text-slate-600">{{ $contact->email }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="crm-container py-8">
    <div class="crm-card overflow-hidden">
        @foreach ($contacts as $contact)
            <div class="border-b border-slate-200 px-4 py-3 last:border-b-0">
                <div class="font-medium">{{ $contact->name }}</div>
                <div class="text-sm text-slate-600">{{ $contact->email }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
