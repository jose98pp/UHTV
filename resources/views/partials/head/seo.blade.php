    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Última Hora TV">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#7c3aed">
    
    <title>@yield('title', 'Última Hora TV - Noticias del Momento')</title>
    
    <!-- Metaetiquetas SEO y Redes Sociales (OpenGraph / Twitter Cards) -->
    @sectionMissing('meta')
        <meta name="description" content="Periódico digital de noticias en Bolivia y el mundo. Cobertura en directo, política, economía, deportes y transmisiones en vivo.">
        <meta property="og:site_name" content="Última Hora TV">
        <meta property="og:title" content="@yield('title', 'Última Hora TV - Noticias del Momento')">
        <meta property="og:description" content="Periódico digital de noticias en Bolivia y el mundo. Cobertura en directo, política, economía, deportes y transmisiones en vivo.">
        <meta property="og:image" content="{{ asset('images/Logo.jpg') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@UHTVBolivia">
        <meta name="twitter:title" content="@yield('title', 'Última Hora TV - Noticias del Momento')">
        <meta name="twitter:description" content="Periódico digital de noticias en Bolivia y el mundo. Cobertura en directo, política, economía, deportes y transmisiones en vivo.">
        <meta name="twitter:image" content="{{ asset('images/Logo.jpg') }}">
    @else
        @yield('meta')
    @endif

    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}">

    <!-- PWA y Metadatos Móviles -->
    @if(config('app.enable_pwa_install', false))
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="UHTV">
    @endif
    <link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/icons/icon.svg') }}">
