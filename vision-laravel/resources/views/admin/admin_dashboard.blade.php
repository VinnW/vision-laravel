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

    {{-- Products Panel --}}
    <div x-show="panel === 'products'" x-cloak>
        @include('admin.sections.products-panel')
    </div>

    {{-- User Panel (belum tersedia) --}}
    <div x-show="panel === 'user'" x-cloak>
        @include('admin.sections.agent-panel')
    </div>

@endsection