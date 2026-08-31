@extends('layouts.public')

@section('content')
    <div class="container mx-auto px-4">
        {{-- Memanggil Section Hero --}}
        @include('public.sections.hero')

        {{-- Dummy Card Konten Tambahan --}}
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <h3 class="text-xl font-semibold mb-2 text-green-600">Status: Berhasil!</h3>
            <p>Layout, Component, dan Section sudah terhubung dengan benar di Laravel Blade.</p>
        </div>
    </div>
@endsection