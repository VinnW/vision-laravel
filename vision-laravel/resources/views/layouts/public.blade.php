<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vision Insurance - Test Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    {{-- Memanggil Header Komponen --}}
    @include('components.header')

    {{-- Tempat Konten Utama Disuntikkan --}}
    <main>
        @yield('content')
    </main>

    {{-- Memanggil Footer Komponen --}}
    @include('components.footer')

</body>
</html>