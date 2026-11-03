<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    @include('partials.head.seo')
    @include('partials.head.schema')
    @include('partials.head.assets')
    @include('partials.banners.shared-styles')
    <style>
        /* Header y navegación permanecen visibles; solo se contrae la franja publicitaria. */
        #uhtv-sticky-shell {
            position: sticky;
            top: 0;
            z-index: 1000;
            isolation: isolate;
        }

        #uhtv-sticky-shell .uhtv-top-ad {
            max-height: 160px;
            overflow: hidden;
            opacity: 1;
            visibility: visible;
            transition: max-height 260ms ease, opacity 180ms ease, visibility 180ms ease;
        }

        #uhtv-sticky-shell.is-scrolled .uhtv-top-ad {
            max-height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border-top-width: 0 !important;
            border-bottom-width: 0 !important;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        @media (prefers-reduced-motion: reduce) {
            #uhtv-sticky-shell .uhtv-top-ad {
                transition: none;
            }
        }
    </style>
    @if(!empty($courtesyMode['active']))
    <style>
        body.uhtv-courtesy-stage-1 { filter: saturate(.88) brightness(.985); }
        body.uhtv-courtesy-stage-2 { filter: saturate(.62) brightness(.96); }
        body.uhtv-courtesy-stage-3 { filter: saturate(.38) brightness(.93); }
    </style>
    @endif
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 {{ !empty($courtesyMode['active']) ? 'uhtv-courtesy-stage-'.$courtesyMode['stage'] : '' }}">
    @if(!empty($courtesyMode['active']))
        <div class="alert alert-warning text-center rounded-0 mb-0 py-2" role="status"><strong>AVISO DE SERVICIO:</strong> {{ $courtesyMode['message'] }}</div>
    @endif
    <div id="uhtv-sticky-shell">
        @include('partials.header.topbar')
        @include('partials.header.navbar')
    </div>

    <main class="container my-4">
        @yield('content')
    </main>

    @include('partials.footer.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stickyShell = document.getElementById('uhtv-sticky-shell');
            if (!stickyShell) return;

            const scrollThreshold = 24;
            let ticking = false;

            function updateStickyHeader() {
                stickyShell.classList.toggle('is-scrolled', window.scrollY > scrollThreshold);
                ticking = false;
            }

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(updateStickyHeader);
                    ticking = true;
                }
            }, { passive: true });

            updateStickyHeader();
        });
    </script>
</body>
</html>
