@extends('layouts.admin')

@section('title', 'Home Panel')

@section('content')

    {{-- Home Panel --}}
    <div x-show="panel === 'home'" x-cloak>
        @include('admin.sections.home-panel')
    </div>

    {{-- Event Panel --}}
    <div x-show="panel === 'event'" x-cloak>
        @include('admin.sections.events-panel')
    </div>

    {{-- User Panel (belum tersedia) --}}
    <div x-show="panel === 'user'" x-cloak>
        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-center">
            <p class="text-sm font-medium text-slate-500">User Panel segera hadir.</p>
        </div>
    </div>

@endsection