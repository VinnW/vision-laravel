<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim((string) ($agent->name ?? '')) ?: $agent->username }} — Agent {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white antialiased">

    @includeFirst(
        ['public.header', 'public.sections.header', 'public.partials.header', 'partials.header', 'layouts.header', 'components.header', 'header'],
        ['navBase' => url('/')]
    )

    @include('agent.sections.agent', ['agent' => $agent, 'isOwner' => $isOwner])

</body>
</html>