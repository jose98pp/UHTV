    <!-- DNS Prefetch for better performance -->
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">

    
    <!-- Preconnect for critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Critical CSS - Load synchronously -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Non-critical CSS - Load asynchronously -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"></noscript>
    
    <!-- Google Fonts with display=swap for better performance -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dashing+Alternate&display=swap" rel="stylesheet">
    

    
    <!-- Optimized CSS - Load with high priority -->
    <link rel="preload" href="{{ asset('css/optimized.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="{{ asset('css/optimized.css') }}" rel="stylesheet"></noscript>
    
    <!-- Assets compilados por Vite (CSS y JS) con protección ante manifest desactualizado -->
    @php
        $manifestPath = public_path('build/manifest.json');
        $manifestData = file_exists($manifestPath) ? json_decode(@file_get_contents($manifestPath), true) : [];
        $hasCompleteManifest = is_array($manifestData) && isset($manifestData['resources/js/app.js']) && isset($manifestData['resources/css/dark-mode.css']);
    @endphp

    @if($hasCompleteManifest)
        @vite(['resources/css/app.css', 'resources/css/browser-compatibility.css', 'resources/css/dark-mode.css', 'resources/css/show-dark-mode.css', 'resources/js/app.js'])
    @elseif(is_array($manifestData) && isset($manifestData['resources/css/app.css']))
        @vite(['resources/css/app.css'])
    @endif

    <!-- Script de inicialización inmediata para modo oscuro -->
    <script>
        // Aplicar modo oscuro inmediatamente para evitar flash
        (function() {
            try {
                const DARK_MODE_KEY = 'uhtv-dark-mode';
                const savedTheme = localStorage.getItem(DARK_MODE_KEY);
                const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                const shouldBeDark = savedTheme === 'dark' || (!savedTheme && systemPrefersDark);
                
                if (shouldBeDark) {
                    document.documentElement.classList.add('dark');
                    if (document.body) {
                        document.body.classList.add('dark');
                    }
                    document.documentElement.style.colorScheme = 'dark';
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    
                    // Actualizar meta theme-color inmediatamente
                    const themeColorMeta = document.querySelector('meta[name="theme-color"]');
                    if (themeColorMeta) {
                        themeColorMeta.setAttribute('content', '#1f2937');
                    }
                } else {
                    document.documentElement.classList.remove('dark');
                    if (document.body) {
                        document.body.classList.remove('dark');
                    }
                    document.documentElement.style.colorScheme = 'light';
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                    
                    // Actualizar meta theme-color inmediatamente
                    const themeColorMeta = document.querySelector('meta[name="theme-color"]');
                    if (themeColorMeta) {
                        themeColorMeta.setAttribute('content', '#7c3aed');
                    }
                }
                
                // Marcar que la inicialización inmediata se completó
                window.darkModeImmediateInit = true;
                window.darkModeInitialState = shouldBeDark;
                
            } catch (e) {
                console.warn('Error in immediate dark mode initialization:', e);
            }
        })();
    </script>

    <!-- Inline critical CSS for immediate rendering -->
    <style>
        /* Critical above-the-fold styles */
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }
        .d-flex { display: flex; }
        .justify-content-center { justify-content: center; }
        .align-items-center { align-items: center; }
        .text-center { text-align: center; }
        
        /* Transiciones suaves para modo oscuro */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Estilos base para modo oscuro */
        .dark {
            color-scheme: dark;
        }
        
        .dark body, body.dark {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
        }
        
        .dark .bg-white, .dark .bg-gray-50, .dark .bg-gray-100 {
            background-color: #1e293b !important;
        }

        .dark .text-gray-900 {
            color: #f1f5f9 !important;
        }
    </style>
