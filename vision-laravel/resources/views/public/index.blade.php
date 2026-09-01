@extends('layouts.public')

@section('content')
    <div class="container mx-auto px-4">
        @include('public.sections.hero')
        @include('public.sections.about')
        @include('public.sections.products')
    </div>
@endsection