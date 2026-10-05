<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php($seo = $page['props']['seo'] ?? [])

        {{-- SEO : rendu côté serveur, visible par Google et les aperçus de partage sans JavaScript --}}
        <title inertia>{{ $seo['title'] ?? config('app.name', 'Laravel') }}</title>
        @if (!empty($seo['description']))
            <meta name="description" content="{{ $seo['description'] }}">
        @endif
        <meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
        @if (!empty($seo['canonical']))
            <link rel="canonical" href="{{ $seo['canonical'] }}">
        @endif

        <meta property="og:site_name" content="{{ $seo['site_name'] ?? config('app.name') }}">
        <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
        <meta property="og:locale" content="{{ $seo['locale'] ?? 'fr_FR' }}">
        <meta property="og:title" content="{{ $seo['title'] ?? config('app.name') }}">
        @if (!empty($seo['description']))
            <meta property="og:description" content="{{ $seo['description'] }}">
        @endif
        @if (!empty($seo['canonical']))
            <meta property="og:url" content="{{ $seo['canonical'] }}">
        @endif
        @if (!empty($seo['image']))
            <meta property="og:image" content="{{ $seo['image'] }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">

        @foreach (($seo['jsonld'] ?? []) as $schema)
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
        @endforeach

        <link rel="icon" href="/favicon.ico">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
