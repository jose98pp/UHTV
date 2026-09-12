<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
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
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="UHTV">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/icons/icon.svg') }}">

    <!-- Datos Estructurados Schema.org (Google NewsMediaOrganization & WebSite) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "NewsMediaOrganization",
          "@id": "{{ url('/') }}/#organization",
          "name": "Última Hora TV",
          "url": "{{ url('/') }}",
          "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/Logo.jpg') }}"
          },
          "sameAs": [
            "https://www.facebook.com/ultimahoratvbolivia",
            "https://www.youtube.com/@UHTVBolivia",
            "https://tiktok.com/@uhtvbolivia"
          ]
        },
        {
          "@type": "WebSite",
          "@id": "{{ url('/') }}/#website",
          "url": "{{ url('/') }}",
          "name": "Última Hora TV",
          "publisher": { "@id": "{{ url('/') }}/#organization" },
          "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/buscar') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        }
      ]
    }
    </script>
    
    <!-- DNS Prefetch for better performance -->
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    
    <!-- Preconnect for critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Critical CSS - Load synchronously -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Non-critical CSS - Load asynchronously -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"></noscript>
    
    <!-- Google Fonts with display=swap for better performance -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dashing+Alternate&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            important: true,
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Optimized CSS - Load with high priority -->
    <link rel="preload" href="{{ asset('css/optimized.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="{{ asset('css/optimized.css') }}" rel="stylesheet"></noscript>
    
    <!-- Assets compilados por Vite (CSS y JS) -->
    @vite([
        'resources/css/app.css',
        'resources/css/browser-compatibility.css',
        'resources/css/dark-mode.css',
        'resources/css/show-dark-mode.css',
        'resources/js/app.jsx'
    ])

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

</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
  <!-- Header Principal estilo El Deber con Imagen de Fondo -->
  <header class="relative bg-gradient-to-r from-purple-800 via-purple-600 to-red-600 dark:from-purple-900 dark:via-purple-700 dark:to-red-700 bg-cover bg-center min-h-[140px] flex items-center justify-center text-white overflow-hidden py-6 px-4 transition-colors duration-300"
          style="background-image: url('{{ isset($banners['portada_top']) && $banners['portada_top']->count() > 0 ? asset($banners['portada_top']->first()->image_path) : asset('images/banner.png') }}');">
    
    <!-- Overlay de oscurecimiento para legibilidad premium -->
    <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/55 to-black/75 dark:from-black/85 dark:via-black/65 dark:to-black/85"></div>
    
    <!-- Elementos decorativos de fondo -->
    <div class="absolute inset-0 opacity-10">
      <div class="absolute top-4 left-4 w-32 h-32 bg-white rounded-full blur-3xl"></div>
      <div class="absolute bottom-4 right-4 w-24 h-24 bg-purple-300 rounded-full blur-2xl"></div>
    </div>

    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center gap-4 relative z-10">
      <!-- Left Column: Weather Animated Ticker -->
      <div class="flex items-center w-full md:w-1/3 justify-start">
        <!-- Ticker de clima animado -->
        <div id="weather-ticker" class="flex items-center transition-all duration-300 opacity-100 transform translate-y-0" style="min-width: 200px;">
          <span class="flex items-center gap-2">
            <i class="fas fa-sun text-yellow-400 text-lg"></i>
            <span class="font-bold text-white text-sm">28°</span>
            <span class="text-xs text-gray-200">- Santa Cruz de la Sierra</span>
          </span>
        </div>
      </div>
      
      <!-- Center Column: Logo y Marca -->
      <div class="flex justify-center w-full md:w-1/3">
        <a href="/" class="flex items-center space-x-3 group">
          <div class="relative">
            <img src="/images/Logo.jpg" alt="UltimaHoraTV" class="w-14 h-14 rounded-full border border-white/20 shadow-md transition-transform duration-300 group-hover:scale-105">
            <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-white/10 to-white/5"></div>
          </div>
          <div class="text-left">
            <h1 class="text-3xl font-extrabold text-white tracking-tight leading-none mb-1">Ultima Hora<span class="text-red-400">TV</span></h1>
            <p class="text-[9px] text-purple-200 font-semibold uppercase tracking-widest leading-none">Noticias Del Momento</p>
          </div>
        </a>
      </div>
      
      <!-- Right Column: BCB Currency Exchange + Live Badge + Dark Mode Toggle -->
      <div class="flex items-center justify-end space-x-3 w-full md:w-1/3">
        <!-- Widget Cotización BCB (Tipo de Cambio Bolivia) -->
        <div class="relative" id="widget-tipo-cambio-bcb">
          <div id="bcb-pill-btn" class="flex items-center gap-2 bg-black/40 hover:bg-black/60 backdrop-blur-md border border-white/20 hover:border-emerald-400/60 px-3 py-1.5 rounded-full text-white shadow-md transition-all duration-300 cursor-pointer select-none group" title="Ver cotizaciones oficiales del BCB">
            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[10px] font-black bg-emerald-500/25 text-emerald-300 border border-emerald-500/40 tracking-wider">
              BCB
            </span>
            <div id="bcb-ticker-display" class="flex items-center gap-1.5 text-xs font-semibold tracking-tight transition-all duration-300">
              <span class="text-white font-bold">USD</span>
              <span class="text-gray-300 text-[10px] font-normal">C:</span><span class="text-emerald-300 font-bold font-mono text-[11px]">6.86</span>
              <span class="text-gray-400 font-light">/</span>
              <span class="text-gray-300 text-[10px] font-normal">V:</span><span class="text-emerald-300 font-bold font-mono text-[11px]">6.96</span>
            </div>
            <i id="bcb-chevron-icon" class="fas fa-chevron-down text-[8px] text-gray-300 group-hover:text-white transition-transform duration-200"></i>
          </div>

          <!-- Dropdown con cotizaciones oficiales BCB -->
          <div id="bcb-dropdown-panel" class="absolute right-0 top-full mt-2 w-64 bg-gray-900/95 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl p-3.5 z-50 hidden opacity-0 transition-all duration-200 text-white">
            <div class="flex items-center justify-between border-b border-white/10 pb-2 mb-2.5">
              <div class="flex items-center gap-2">
                <i class="fas fa-landmark text-emerald-400 text-sm"></i>
                <span class="font-bold text-xs uppercase tracking-wide text-white">Cotización Oficial</span>
              </div>
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-semibold border border-emerald-500/30">BCB Bolivia</span>
            </div>

            <div class="space-y-2 text-xs">
              <!-- Dólar Estadounidense -->
              <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10 hover:border-emerald-500/30 transition-all">
                <div class="flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-xs font-bold">$</span>
                  <div>
                    <div class="font-bold text-white text-xs leading-none">Dólar (USD)</div>
                    <div class="text-[10px] text-gray-400 leading-tight">Tipo de Cambio Oficial</div>
                  </div>
                </div>
                <div class="text-right font-mono">
                  <div class="text-[11px]"><span class="text-gray-400 text-[10px]">Compra:</span> <span class="text-emerald-300 font-bold">6.86 Bs</span></div>
                  <div class="text-[11px]"><span class="text-gray-400 text-[10px]">Venta:</span> <span class="text-emerald-300 font-bold">6.96 Bs</span></div>
                </div>
              </div>

              <!-- Euro -->
              <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10 hover:border-blue-500/30 transition-all">
                <div class="flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-blue-500/20 text-blue-300 flex items-center justify-center text-xs font-bold">€</span>
                  <div>
                    <div class="font-bold text-white text-xs leading-none">Euro (EUR)</div>
                    <div class="text-[10px] text-gray-400 leading-tight">Referencial BCB</div>
                  </div>
                </div>
                <div class="text-right font-mono text-[11px]">
                  <span class="text-blue-300 font-bold">7.45 Bs</span>
                </div>
              </div>

              <!-- UFV -->
              <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10 hover:border-amber-500/30 transition-all">
                <div class="flex items-center gap-2">
                  <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-300 flex items-center justify-center text-xs font-bold">U</span>
                  <div>
                    <div class="font-bold text-white text-xs leading-none">UFV</div>
                    <div class="text-[10px] text-gray-400 leading-tight">Unidad Fomento Vivienda</div>
                  </div>
                </div>
                <div class="text-right font-mono text-[11px]">
                  <span class="text-amber-300 font-bold">2.54 Bs</span>
                </div>
              </div>
            </div>

            <div class="mt-2.5 pt-2 border-t border-white/10 text-[9px] text-gray-400 text-center flex items-center justify-center gap-1.5">
              <i class="fas fa-check-circle text-emerald-400 text-[9px]"></i>
              <span>Fuente oficial Banco Central de Bolivia</span>
            </div>
          </div>
        </div>

        @if(isset($transmisionEnVivo) && $transmisionEnVivo)
          <button type="button" 
                  data-open-live-modal 
                  data-stream-embed="{{ $transmisionEnVivo->embed_url }}"
                  data-stream-title="{{ $transmisionEnVivo->titulo }}"
                  class="group relative inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-gradient-to-r from-red-600 via-red-500 to-red-600 hover:from-red-500 hover:to-red-700 text-white text-[11px] font-extrabold uppercase tracking-wider shadow-lg hover:shadow-red-500/40 transition-all duration-300 transform hover:scale-105 focus:outline-none cursor-pointer border border-white/20"
                  title="Transmitiendo En Vivo: {{ $transmisionEnVivo->titulo }}">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
            </span>
            <span>En Vivo</span>
            <i class="fas fa-play text-[8px] opacity-80 group-hover:opacity-100 transition-opacity"></i>
          </button>
        @endif
        <!-- Selector de tema oscuro -->
        <button data-dark-mode-toggle class="p-2 rounded-full hover:bg-white/10 text-white transition-colors" aria-label="Cambiar tema" style="background: none; border: none; cursor: pointer;">
          <i class="fas fa-sun text-yellow-400 sun-icon hidden"></i>
          <i class="fas fa-moon moon-icon text-gray-100"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Script del Clima Rotativo Dinámico y Cotizaciones BCB -->
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          // 1. Lógica de Rotación y Menú de Tipo de Cambio BCB
          const bcbTicker = document.getElementById('bcb-ticker-display');
          const bcbContainer = document.getElementById('widget-tipo-cambio-bcb');
          const bcbPillBtn = document.getElementById('bcb-pill-btn');
          const bcbDropdown = document.getElementById('bcb-dropdown-panel');
          const bcbChevron = document.getElementById('bcb-chevron-icon');

          if (bcbTicker && bcbPillBtn && bcbDropdown) {
              const rates = [
                  '<span class="text-white font-bold">USD</span> <span class="text-gray-300 text-[10px] font-normal">C:</span><span class="text-emerald-300 font-bold font-mono text-[11px]">6.86</span> <span class="text-gray-400 font-light">/</span> <span class="text-gray-300 text-[10px] font-normal">V:</span><span class="text-emerald-300 font-bold font-mono text-[11px]">6.96</span>',
                  '<span class="text-white font-bold">EUR</span> <span class="text-gray-300 text-[10px] font-normal">Oficial:</span><span class="text-blue-300 font-bold font-mono text-[11px]">7.45 Bs</span>',
                  '<span class="text-white font-bold">UFV</span> <span class="text-gray-300 text-[10px] font-normal">Valor:</span><span class="text-amber-300 font-bold font-mono text-[11px]">2.54 Bs</span>'
              ];
              let currentRateIndex = 0;
              let isDropdownOpen = false;

              function toggleBcbDropdown(show) {
                  isDropdownOpen = (show !== undefined) ? show : bcbDropdown.classList.contains('hidden');
                  if (isDropdownOpen) {
                      bcbDropdown.classList.remove('hidden');
                      requestAnimationFrame(() => {
                          bcbDropdown.classList.remove('opacity-0');
                          if (bcbChevron) bcbChevron.classList.add('rotate-180');
                      });
                  } else {
                      bcbDropdown.classList.add('opacity-0');
                      if (bcbChevron) bcbChevron.classList.remove('rotate-180');
                      setTimeout(() => {
                          if (!isDropdownOpen) bcbDropdown.classList.add('hidden');
                      }, 200);
                  }
              }

              bcbPillBtn.addEventListener('click', function(e) {
                  e.stopPropagation();
                  toggleBcbDropdown();
              });

              bcbContainer.addEventListener('mouseenter', function() {
                  toggleBcbDropdown(true);
              });

              bcbContainer.addEventListener('mouseleave', function() {
                  toggleBcbDropdown(false);
              });

              document.addEventListener('click', function(e) {
                  if (bcbContainer && !bcbContainer.contains(e.target)) {
                      toggleBcbDropdown(false);
                  }
              });

              setInterval(function() {
                  if (isDropdownOpen) return;
                  currentRateIndex = (currentRateIndex + 1) % rates.length;
                  bcbTicker.style.opacity = '0';
                  setTimeout(function() {
                      bcbTicker.innerHTML = rates[currentRateIndex];
                      bcbTicker.style.opacity = '1';
                  }, 250);
              }, 4500);
          }

          // 2. Lógica del Clima Rotativo Animado
          const weatherData = [
              { id: 'sc', name: 'Santa Cruz de la Sierra', temp: 28, code: 0, lat: -17.7863, lon: -63.1812 },
              { id: 'lp', name: 'La Paz', temp: 15, code: 3, lat: -16.5001, lon: -68.1193 },
              { id: 'cb', name: 'Cochabamba', temp: 22, code: 2, lat: -17.3895, lon: -66.1568 }
          ];

          let currentIndex = 0;
          const ticker = document.getElementById('weather-ticker');

          function getWeatherIconClass(code) {
              if (code === 0) return 'fa-sun text-yellow-400';
              if ([1, 2].includes(code)) return 'fa-cloud-sun text-yellow-300';
              if (code === 3) return 'fa-cloud text-gray-300';
              if ([45, 48].includes(code)) return 'fa-smog text-gray-400';
              if ([51, 53, 55, 56, 57, 80, 81, 82].includes(code)) return 'fa-cloud-rain text-blue-300';
              if ([61, 63, 65, 66, 67].includes(code)) return 'fa-cloud-showers-heavy text-blue-400';
              if ([71, 73, 75, 77, 85, 86].includes(code)) return 'fa-snowflake text-blue-100';
              if ([95, 96, 99].includes(code)) return 'fa-cloud-bolt text-yellow-300';
              return 'fa-cloud-sun text-yellow-300';
          }

          async function updateAllWeather() {
              for (let city of weatherData) {
                  try {
                      const url = `https://api.open-meteo.com/v1/forecast?latitude=${city.lat}&longitude=${city.lon}&current=temperature_2m,weather_code`;
                      const response = await fetch(url);
                      if (response.ok) {
                          const data = await response.json();
                          city.temp = Math.round(data.current.temperature_2m);
                          city.code = data.current.weather_code;
                      }
                  } catch (e) {
                      console.warn(`Error fetching weather for ${city.name}:`, e);
                  }
              }
              renderWeather();
          }

          function renderWeather() {
              if (!ticker) return;
              const city = weatherData[currentIndex];
              const iconClass = getWeatherIconClass(city.code);
              
              ticker.innerHTML = `
                  <span class="flex items-center gap-1.5 transition-all duration-300">
                      <i class="fas ${iconClass} text-lg"></i>
                      <span class="font-bold text-white text-sm">${city.temp}°</span>
                      <span class="text-xs text-gray-200">- ${city.name}</span>
                  </span>
              `;
          }

          function rotateWeather() {
              if (!ticker) return;
              ticker.style.opacity = '0';
              ticker.style.transform = 'translateY(-5px)';
              
              setTimeout(() => {
                  currentIndex = (currentIndex + 1) % weatherData.length;
                  renderWeather();
                  ticker.style.opacity = '1';
                  ticker.style.transform = 'translateY(0)';
              }, 300);
          }

          // Inicializar clima rotativo
          updateAllWeather();
          setInterval(rotateWeather, 3800);
      });
  </script>

<!-- Navbar Mejorada -->
<nav class="bg-white dark:bg-gray-900 shadow-xl sticky top-0 left-0 w-full z-50 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
 <div class="container mx-auto flex justify-between items-center py-4 px-4">
  <!-- Logo/Inicio -->
  <a href="/" class="flex items-center space-x-2 text-gray-900 dark:text-gray-100 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-300">
    <i class="fas fa-home text-xl"></i>
    <span class="text-xl font-bold">Inicio</span>
  </a>
  
  <!-- Botón del menú hamburguesa mejorado -->
  <button id="hamburgerButton" class="lg:hidden text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 focus:outline-none focus:text-purple-600 dark:focus:text-purple-400 transition-colors duration-300">
    <div class="w-6 h-6 flex flex-col justify-center items-center">
      <span class="block w-6 h-0.5 bg-current transition-all duration-300 transform"></span>
      <span class="block w-6 h-0.5 bg-current mt-1 transition-all duration-300 transform"></span>
      <span class="block w-6 h-0.5 bg-current mt-1 transition-all duration-300 transform"></span>
    </div>
  </button>
  
  <!-- Menú para pantallas grandes -->
  <div class="hidden lg:flex space-x-8 mx-auto">
    @forelse($categorias ?? [] as $categoria)
      <a href="{{ $categoria->url }}" 
         class="text-gray-700 dark:text-gray-300 font-semibold hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-300 relative group py-2">
        {{ $categoria->name }}
        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-purple-600 to-red-600 transition-all duration-300 group-hover:w-full"></span>
      </a>
    @empty
      <span class="text-gray-500 dark:text-gray-400 italic">No hay categorías disponibles</span>
    @endforelse
  </div>
  
  <!-- Búsqueda y Controles -->
  <div class="hidden lg:flex items-center space-x-4">
    <!-- Formulario de búsqueda -->
    <form action="{{ route('search') }}" method="GET" class="relative">
      <input type="text" 
             name="q" 
             placeholder="Buscar noticias..." 
             class="w-64 px-4 py-2 pl-10 pr-4 text-sm text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
      <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm"></i>
      <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-purple-600 hover:bg-purple-700 text-white p-1.5 rounded-full transition-colors duration-300">
        <i class="fas fa-arrow-right text-xs"></i>
      </button>
    </form>
    
  </div>
</div>

  <!-- Sidebar para móviles mejorado -->
  <div
    id="mobileMenu"
    class="fixed top-0 left-0 h-full w-80 bg-white dark:bg-gray-900 shadow-2xl transform -translate-x-full transition-all duration-300 lg:hidden z-50 overflow-y-auto"
  >
    <!-- Header del menú móvil -->
    <div class="bg-gradient-to-r from-purple-600 to-red-600 text-white p-6">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-3">
          <img src="/images/Logo.jpg" alt="UHTV" class="w-10 h-10 rounded-full" loading="lazy" decoding="async">
          <span class="text-xl font-bold">UHTV</span>
        </div>
        <button id="closeMenu" class="text-white hover:text-gray-200 focus:outline-none transition-colors duration-300">
          <i class="fas fa-times text-2xl"></i>
        </button>
      </div>
    </div>
    
    <!-- Navegación principal -->
    <div class="p-6">
      <a href="/" class="flex items-center space-x-3 text-gray-800 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-300 py-3 border-b border-gray-100 dark:border-gray-700">
        <i class="fas fa-home text-lg"></i>
        <span class="font-semibold">Inicio</span>
      </a>
      @if(isset($transmisionEnVivo) && $transmisionEnVivo)
        <a href="{{ route('transmisiones.en-vivo') }}" class="flex items-center space-x-3 text-red-600 dark:text-red-400 hover:text-red-700 font-semibold py-3 border-b border-gray-100 dark:border-gray-700">
          <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>
          </span>
          <span>🔴 En Vivo Ahora</span>
        </a>
      @endif
      
      <!-- Búsqueda móvil -->
      <div class="mt-4">
        <form action="{{ route('search') }}" method="GET" class="relative">
          <input type="text" 
                 name="q" 
                 placeholder="Buscar noticias..." 
                 class="w-full px-4 py-3 pl-10 pr-4 text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
          <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
          <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-lg transition-colors duration-300">
            <i class="fas fa-arrow-right text-sm"></i>
          </button>
        </form>
      </div>
    </div>
    
    <!-- Categorías -->
    <div class="px-6">
      <h3 class="text-gray-500 dark:text-gray-400 uppercase text-sm font-semibold tracking-wide mb-4">Categorías</h3>
      <div class="space-y-2">
        @forelse($categorias ?? [] as $categoria)
          <a href="{{ $categoria->url }}" 
             class="flex items-center space-x-3 text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-300 py-3 px-4 rounded-lg">
            <i class="fas fa-folder text-sm"></i>
            <span class="font-medium">{{ $categoria->name }}</span>
          </a>
        @empty
          <span class="text-gray-500 dark:text-gray-400 italic text-sm">No hay categorías disponibles</span>
        @endforelse
      </div>
    </div>
    
    <!-- Redes sociales en el menú móvil -->
    <div class="p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-4">
      <h4 class="text-gray-500 dark:text-gray-400 uppercase text-xs font-semibold tracking-wide mb-3">Síguenos</h4>
      <div class="flex space-x-3">
        <a href="https://facebook.com/uhtvbolivia" target="_blank" class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition-colors duration-300">
          <i class="fab fa-facebook text-sm"></i>
        </a>
        <a href="https://www.youtube.com/@UHTVBolivia" target="_blank" class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors duration-300">
          <i class="fab fa-youtube text-sm"></i>
        </a>
        <a href="https://instagram.com/uhtvbolivia" target="_blank" class="bg-pink-600 text-white p-2 rounded-full hover:bg-pink-700 transition-colors duration-300">
          <i class="fab fa-instagram text-sm"></i>
        </a>
        <a href="https://x.com/UhtvBol" target="_blank" class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-900 transition-colors duration-300">
          <i class="fab fa-x-twitter text-sm"></i>
        </a>
        <a href="https://tiktok.com/@uhtvbolivia" target="_blank" class="bg-black text-white p-2 rounded-full hover:bg-gray-800 transition-colors duration-300">
          <i class="fab fa-tiktok text-sm"></i>
        </a>
      </div>
    </div>
  </div>
  
  <!-- Overlay para cerrar el menú -->
  <div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden z-40"></div>
</nav>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const hamburgerButton = document.getElementById('hamburgerButton');
    const closeMenu = document.getElementById('closeMenu');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    function openMobileMenu() {
      mobileMenu.classList.remove('-translate-x-full');
      mobileMenuOverlay.classList.remove('opacity-0', 'pointer-events-none');
      document.body.style.overflow = 'hidden';
      
      // Animación del botón hamburguesa
      const spans = hamburgerButton.querySelectorAll('span');
      spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
      spans[1].style.opacity = '0';
      spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
    }

    function closeMobileMenu() {
      mobileMenu.classList.add('-translate-x-full');
      mobileMenuOverlay.classList.add('opacity-0', 'pointer-events-none');
      document.body.style.overflow = '';
      
      // Restaurar botón hamburguesa
      const spans = hamburgerButton.querySelectorAll('span');
      spans[0].style.transform = '';
      spans[1].style.opacity = '';
      spans[2].style.transform = '';
    }

    // Event listeners
    if (hamburgerButton) {
      hamburgerButton.addEventListener('click', openMobileMenu);
    }

    if (closeMenu) {
      closeMenu.addEventListener('click', closeMobileMenu);
    }

    if (mobileMenuOverlay) {
      mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    }

    // Cerrar menú al hacer clic en un enlace
    const mobileMenuLinks = mobileMenu.querySelectorAll('a');
    mobileMenuLinks.forEach(link => {
      link.addEventListener('click', closeMobileMenu);
    });

    // Cerrar menú con tecla Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && !mobileMenu.classList.contains('-translate-x-full')) {
        closeMobileMenu();
      }
    });
  });
</script>

  @if(isset($transmisionEnVivo) && $transmisionEnVivo)
    <!-- Alerta En Vivo Principal -->
    <aside id="liveStreamAlert" class="bg-gradient-to-r from-red-700 via-red-600 to-red-800 text-white shadow-xl border-y border-red-500/40 relative z-30 transition-all duration-300" aria-label="Alerta de Transmisión en Vivo">
      <div class="container mx-auto px-4 py-2.5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-3 overflow-hidden min-w-0">
          <span class="relative flex h-3 w-3 flex-shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-90"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
          </span>
          <span class="bg-black/40 text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full border border-white/20 flex-shrink-0">
            🔴 EN VIVO AHORA
          </span>
          <span class="text-xs sm:text-sm font-bold truncate text-white">
            {{ $transmisionEnVivo->titulo }}
          </span>
        </div>
        <div class="flex items-center space-x-2 flex-shrink-0 ml-auto">
          <button type="button" 
                  data-open-live-modal 
                  data-stream-embed="{{ $transmisionEnVivo->embed_url }}"
                  data-stream-title="{{ $transmisionEnVivo->titulo }}"
                  class="bg-white text-red-600 hover:bg-gray-100 font-extrabold text-xs px-3.5 py-1.5 rounded-full shadow transition-all duration-200 flex items-center gap-1.5 transform hover:scale-105 cursor-pointer">
            <i class="fas fa-play text-[9px]"></i>
            <span>Ver Transmisión</span>
          </button>
          <button type="button" 
                  onclick="document.getElementById('liveStreamAlert').style.display='none'" 
                  class="text-white/80 hover:text-white p-1 focus:outline-none transition-colors" 
                  title="Cerrar aviso">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>
      </div>
    </aside>
  @endif

    <!-- Contenido Principal -->
    <main class="container my-4">
        @yield('content')
    </main>

    <!-- Banner Publicitario Footer (Pre-Footer) -->
    @if(isset($banners['footer']) && $banners['footer']->count() > 0)
        <div class="publicidad-footer w-full flex flex-col justify-center items-center my-8 px-4">
            <div class="text-center mb-2">
                <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold uppercase tracking-wider">Publicidad</span>
            </div>
            @foreach($banners['footer'] as $banner)
                <div class="w-full flex justify-center mb-4">
                    <a href="{{ $banner->link ?? '#' }}" target="_blank" rel="noopener noreferrer" class="block w-full max-w-5xl transition-transform hover:scale-[1.01] duration-300 group"> 
                        <img src="{{ asset($banner->image_path) }}" 
                             alt="{{ $banner->title }}" 
                             class="w-full h-auto rounded-2xl shadow-lg object-cover border border-gray-200 dark:border-gray-700" 
                             loading="lazy"
                             decoding="async">
                    </a>
                </div>
            @endforeach
        </div>
    @endif

   <!-- Footer Moderno - Inspirado en la imagen -->
<footer class="bg-gray-800 dark:bg-gray-900 text-white py-12 border-t border-gray-700">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Logo y Descripción -->
            <div class="lg:col-span-1">
                <div class="flex items-center space-x-3 mb-4">
                    <img src="{{ asset('images/Logo.jpg') }}" alt="ÚltimaHoraTV" class="w-12 h-12 rounded-full" loading="lazy" decoding="async">
                    <div>
                        <h3 class="text-xl font-bold text-white">Última<span class="text-purple-400">Hora</span> TV</h3>
                        <p class="text-gray-400 text-sm">Tu fuente confiable de noticias y análisis. © {{ date('Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Secciones -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Secciones</h4>
                <ul class="space-y-2">
                    @forelse($categorias ?? [] as $categoria)
                        <li>
                            <a href="{{ $categoria->url }}" 
                               class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                                {{ $categoria->name }}
                            </a>
                        </li>
                    @empty
                        <li class="text-gray-500 text-sm">Política</li>
                        <li class="text-gray-500 text-sm">Deportes</li>
                        <li class="text-gray-500 text-sm">Negocios</li>
                        <li class="text-gray-500 text-sm">Tecnología</li>
                    @endforelse
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Legal</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                            Aviso de Privacidad
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                            Términos y Condiciones
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                            Contacto
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                            Acerca de Nosotros
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Síguenos -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Síguenos</h4>
                <div class="flex space-x-3 mb-4">
                    <a href="https://facebook.com/uhtvbolivia" target="_blank" rel="noopener noreferrer" 
                       class="bg-gray-700 hover:bg-blue-600 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-facebook text-lg"></i>
                    </a>
                    <a href="https://x.com/UhtvBol" target="_blank" rel="noopener noreferrer" 
                       class="bg-gray-700 hover:bg-gray-900 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-x-twitter text-lg"></i>
                    </a>
                    <a href="https://instagram.com/uhtvbolivia" target="_blank" rel="noopener noreferrer" 
                       class="bg-gray-700 hover:bg-pink-600 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="https://tiktok.com/@uhtvbolivia" target="_blank" rel="noopener noreferrer" 
                       class="bg-gray-700 hover:bg-black text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-tiktok text-lg"></i>
                    </a>
                </div>
                
                <!-- Toggle de modo oscuro en footer -->
                <div class="mt-4">
                    <button data-dark-mode-toggle 
                            class="flex items-center space-x-2 text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                        <i class="fas fa-sun sun-icon hidden"></i>
                        <i class="fas fa-moon moon-icon"></i>
                        <span class="sun-text hidden">Modo Claro</span>
                        <span class="moon-text">Modo Oscuro</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Línea divisoria -->
        <div class="border-t border-gray-700 mt-8 pt-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">
                    Desarrollado con <i class="fas fa-heart text-red-500 mx-1"></i> por el equipo de Última Hora TV.
                </p>
                <div class="flex items-center space-x-4 text-gray-400 text-sm">
                    <span>Bolivia</span>
                    <span>•</span>
                    <span>{{ date('Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</footer>
    <!-- Dark Mode Script (debe cargarse antes que otros scripts) -->
    <script src="{{ asset('js/dark-mode.js') }}"></script>
    
    <!-- CSS Optimization and Browser Compatibility -->
    <script src="{{ asset('js/css-optimization.js') }}"></script>
    
    <!-- Diagnostics (solo en desarrollo) -->
    <script src="{{ asset('js/diagnostics.js') }}"></script>
    
    <!-- Error Handler -->
    <script src="{{ asset('js/error-handler.js') }}"></script>
    
    <!-- Bootstrap JS - Solo una versión -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

@if(isset($banners['popup']) && $banners['popup']->count() > 0)
  @php
    $popupBanner = $banners['popup']->first();
  @endphp
  <!-- Modal Publicitario Emergente Global -->
  <div id="promoModal" style="position: fixed; inset: 0; z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(4px); transition: opacity 0.3s ease; opacity: 0; pointer-events: none;">
    <div id="promoModalContent" class="relative max-w-lg w-full mx-4" style="transform: scale(0.9); opacity: 0; transition: transform 0.3s ease, opacity 0.3s ease;">
      
      <!-- Contenedor con marco grueso gris oscuro e imagen -->
      <div class="relative bg-black rounded overflow-hidden shadow-2xl" style="border: 8px solid #2d3748; outline: 2px solid #1a202c;">
        
        <!-- Botón de cerrar rojo 'X' en la esquina superior derecha interna -->
        <button onclick="closePromoModal()" class="absolute top-3 right-4 z-10 text-red-500 hover:text-red-400 transition-colors duration-200 focus:outline-none" aria-label="Cerrar" style="background: none; border: none; cursor: pointer;">
          <i class="fas fa-times text-2xl filter drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]"></i>
        </button>

        <a href="{{ $popupBanner->link ?? '#' }}" {{ $popupBanner->link ? 'target="_blank" rel="noopener noreferrer"' : '' }} class="block overflow-hidden group">
          <img src="{{ asset($popupBanner->image_path) }}" alt="{{ $popupBanner->title }}" 
               class="w-full h-auto object-cover max-h-[70vh] transition-transform duration-500 group-hover:scale-[1.01]"
               loading="lazy"
               decoding="async">
        </a>
      </div>
      
      <!-- Indicación de teclado sutil debajo del modal -->
      <div class="text-center mt-4 animate-pulse">
        <span class="text-xs bg-black/60 text-gray-300 px-3 py-1.5 rounded-full border border-white/10 uppercase tracking-widest font-mono shadow-md">Presiona [Z] para cerrar</span>
      </div>
      
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('promoModal');
        const modalContent = document.getElementById('promoModalContent');
        
        if (modal && modalContent) {
            // Mostrar modal tras 1.5 segundos
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.style.pointerEvents = 'auto';
                modalContent.style.transform = 'scale(1)';
                modalContent.style.opacity = '1';
                
                // Prevenir scroll en la página mientras el modal está abierto
                document.body.style.overflow = 'hidden';
            }, 1500);
        }

        // Escuchador de teclado para la tecla 'z' o 'Z'
        document.addEventListener('keydown', function(e) {
            if (e.key === 'z' || e.key === 'Z' || e.keyCode === 90) {
                closePromoModal();
            }
        });
    });

    function closePromoModal() {
        const modal = document.getElementById('promoModal');
        const modalContent = document.getElementById('promoModalContent');
        
        if (modal && modalContent) {
            modalContent.style.transform = 'scale(0.9)';
            modalContent.style.opacity = '0';
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            
            // Restaurar scroll de la página
            document.body.style.overflow = '';
        }
    }
  </script>
@endif

  <!-- Modal de Streaming En Vivo y Podcasts -->
  @include('partials.live-modal')

  <!-- Banner Flotante de Instalación PWA (App Móvil) -->
  <div id="pwa-install-banner" class="fixed bottom-4 left-4 right-4 md:left-auto md:right-6 md:w-96 bg-gray-900/95 backdrop-blur-md text-white p-4 rounded-2xl shadow-2xl border border-purple-500/30 z-50 transform translate-y-32 opacity-0 transition-all duration-500 pointer-events-none flex items-center justify-between gap-3">
      <div class="flex items-center gap-3">
          <img src="{{ asset('images/icons/icon-192x192.png') }}" alt="UHTV App" class="w-12 h-12 rounded-xl shadow-md border border-white/20 flex-shrink-0" loading="lazy" decoding="async">
          <div>
              <h6 class="font-bold text-sm text-white leading-tight">Instalar App UHTV</h6>
              <p class="text-xs text-purple-200 mt-0.5">Accede al instante y lee noticias sin conexión.</p>
          </div>
      </div>
      <div class="flex items-center gap-2">
          <button id="pwa-install-btn" class="bg-gradient-to-r from-purple-600 to-red-600 hover:from-purple-700 hover:to-red-700 text-white text-xs font-bold px-3 py-2 rounded-xl shadow-md transition-all transform hover:scale-105">
              Instalar
          </button>
          <button id="pwa-dismiss-btn" class="text-gray-400 hover:text-white p-1.5 text-xs transition-colors" title="Cerrar">
              <i class="fas fa-times"></i>
          </button>
      </div>
  </div>

  <script>
    // Registro de Service Worker para PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(reg) {
                    console.log('[PWA] Service Worker registrado con éxito:', reg.scope);
                })
                .catch(function(err) {
                    console.warn('[PWA] Error al registrar Service Worker:', err);
                });
        });
    }

    // Manejo del aviso de instalación nativa PWA
    let deferredInstallPrompt = null;
    const installBanner = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredInstallPrompt = e;

        if (!localStorage.getItem('uhtv-pwa-dismissed')) {
            if (installBanner) {
                installBanner.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            }
        }
    });

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredInstallPrompt) return;
            deferredInstallPrompt.prompt();
            const { outcome } = await deferredInstallPrompt.userChoice;
            console.log('[PWA] Respuesta del usuario:', outcome);
            deferredInstallPrompt = null;
            if (installBanner) {
                installBanner.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
            }
        });
    }

    if (dismissBtn) {
        dismissBtn.addEventListener('click', () => {
            if (installBanner) {
                installBanner.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
                localStorage.setItem('uhtv-pwa-dismissed', 'true');
            }
        });
    }

    window.addEventListener('appinstalled', () => {
        console.log('[PWA] Aplicación UHTV instalada exitosamente');
        if (installBanner) {
            installBanner.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
        }
    });
  </script>
</body>

</html>


