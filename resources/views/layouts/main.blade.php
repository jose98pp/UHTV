<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    @include('partials.head.seo')
    @include('partials.head.schema')
    @include('partials.head.assets')
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    @include('partials.header.topbar')
    @include('partials.header.navbar')

    <main class="container my-4">
        @yield('content')
    </main>

    @include('partials.footer.footer')
</body>
</html>
