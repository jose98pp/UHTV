  <!-- Estilos dedicados para sincronizar exactamente la altura del Header con el Banner Publicitario -->
  <style>
    .uhtv-header-matched-height {
            height: auto;
            min-height: 0;
            aspect-ratio: 3840 / 397;
        }
        .uhtv-header-art {
            top: 41px;
            height: calc(100% - 41px);
        }
        .uhtv-header-heritage-link,
        .uhtv-header-brand-link {
            height: 32px;
        }
        @media (min-width: 640px) {
            .uhtv-header-heritage-link,
            .uhtv-header-brand-link {
                height: 40px;
            }
            .uhtv-header-art {
                top: 47px;
                height: calc(100% - 47px);
            }
        }
        @media (min-width: 768px) {
            .uhtv-header-matched-height {
                height: 140px;
                min-height: 140px;
                aspect-ratio: auto;
            }
            .uhtv-header-art {
                top: 0;
                height: 100%;
            }
            .uhtv-header-heritage-link {
                height: 64px;
            }
            .uhtv-header-brand-link {
                height: 56px;
            }
    }
  </style>

  <!-- Header Principal con Imagen de Fondo Herencia Invisible + UHTV en Ultra HD -->
  <header id="uhtv-main-header"
          class="relative flex flex-col md:flex-row items-center justify-between text-white select-none border-b border-purple-800/40"
          style="background: linear-gradient(90deg, #5b5be3 0%, #4033b0 45%, #010102 100%);">
    
    <!-- Imagen de cabecera en Ultra HD 4K (SOLO esta capa tiene overflow-hidden para no recortar el menú del dólar) -->
    <div class="uhtv-header-art absolute inset-x-0 w-full flex items-center justify-center pointer-events-none overflow-hidden md:inset-y-0">
      <img src="{{ asset('images/header-herencia-uhtv-hd.png') }}"
           alt="Herencia Invisible - Última Hora TV"
           class="w-full h-full select-none"
           style="object-fit: contain; object-position: center;"
           loading="eager"
           decoding="async">
    </div>

    <!-- Barra de Widgets (Clima + Dólar BCB + En Vivo + Modo Oscuro):
         - En móvil (<md): Barra superior compacta (order-1) con glassmorphism, dejando el logo totalmente despejado.
         - En desktop (md+): Flotante a la derecha del header dentro de los 140px (order-2), verticalmente centrada.
    -->
    <div class="w-full md:w-auto md:absolute md:right-4 md:top-1/2 md:-translate-y-1/2 flex items-center justify-between md:justify-end gap-1.5 sm:gap-2 px-3 py-1.5 md:p-0 bg-black/45 md:bg-transparent backdrop-blur-md md:backdrop-blur-none border-b md:border-none border-white/10 relative z-30 order-1 md:order-2">
      @include('partials.header.weather')
      @include('partials.header.currency')
    </div>

    <!-- Área del Logo (140px de alto perfectamente sincronizado con el banner publicitario):
         - En móvil: 140px completamente limpios y libres para lucir el logo sin superposición de texto ni widgets.
         - En desktop: Distribuye los accesos clickeables a los logos.
    -->
    <div class="container mx-auto flex items-center justify-between md:justify-start w-full md:w-2/3 uhtv-header-matched-height relative z-10 px-2 sm:px-4 py-1 order-2 md:order-1">
      <!-- Columna Izquierda: Herencia Invisible -->
      <div class="flex items-center justify-start md:w-1/2">
        <a href="/" class="uhtv-header-heritage-link block w-28 sm:w-44 md:w-52 rounded-xl hover:bg-white/5 transition-all" title="Herencia Invisible">
          <span class="sr-only">Herencia Invisible</span>
        </a>
      </div>

      <!-- Columna Central: Última Hora TV -->
      <div class="flex items-center justify-center md:w-1/2">
        @include('partials.header.logo')
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
                  '<span class="text-white font-bold">USD</span> <span class="text-purple-200 text-[10px] font-normal">C:</span><span id="bcb-pill-compra" class="text-emerald-300 font-bold font-mono text-[11px]">11.00</span> <span class="text-white/40 font-light">/</span> <span class="text-purple-200 text-[10px] font-normal">V:</span><span id="bcb-pill-venta" class="text-emerald-300 font-bold font-mono text-[11px]">11.10</span> <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse ml-0.5" title="En tiempo real"></span>',
                  '<span class="text-emerald-300 font-bold">BCB</span> <span class="text-purple-200 text-[10px]">Régimen:</span> <span class="text-amber-300 font-bold text-[11px] uppercase">Flexible</span>',
                  '<span class="text-white font-bold">EUR</span> <span class="text-purple-200 text-[10px] font-normal">Ref:</span><span class="text-blue-300 font-bold font-mono text-[11px]">11.85 Bs</span>',
                  '<span class="text-white font-bold">UFV</span> <span class="text-purple-200 text-[10px] font-normal">Hoy:</span><span class="text-amber-300 font-bold font-mono text-[11px]">2.54 Bs</span>'
              ];
              let currentRateIndex = 0;
              let isDropdownOpen = false;

              function toggleBcbDropdown(show) {
                  isDropdownOpen = (show !== undefined) ? show : bcbDropdown.classList.contains('hidden');
                  const headerEl = document.getElementById('uhtv-main-header');
                  if (isDropdownOpen) {
                      if (headerEl) headerEl.style.zIndex = '60';
                      bcbDropdown.classList.remove('hidden');
                      requestAnimationFrame(() => {
                          bcbDropdown.classList.remove('opacity-0');
                          if (bcbChevron) bcbChevron.classList.add('rotate-180');
                      });
                  } else {
                      bcbDropdown.classList.add('opacity-0');
                      if (bcbChevron) bcbChevron.classList.remove('rotate-180');
                      setTimeout(() => {
                          if (!isDropdownOpen) {
                              bcbDropdown.classList.add('hidden');
                              if (headerEl) headerEl.style.zIndex = '';
                          }
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

              window.addEventListener('scroll', function() {
                  if (isDropdownOpen) {
                      toggleBcbDropdown(false);
                  }
              }, { passive: true });

              setInterval(function() {
                  if (isDropdownOpen) return;
                  currentRateIndex = (currentRateIndex + 1) % rates.length;
                  bcbTicker.style.opacity = '0';
                  setTimeout(function() {
                      bcbTicker.innerHTML = rates[currentRateIndex];
                      bcbTicker.style.opacity = '1';
                  }, 250);
              }, 4500);

              // 1.1 Lógica de Actualización de Datos en Tiempo Real (BCB Bolivia)
              function updateBcbDOM(data) {
                  const compra = (typeof data.compra === 'number') ? data.compra.toFixed(2) : data.compra;
                  const venta = (typeof data.venta === 'number') ? data.venta.toFixed(2) : data.venta;
                  const regimen = data.regimen ? data.regimen.toUpperCase() : 'FLEXIBLE';
                  const fecha = data.fecha || '';
                  const eur = data.eur ? Number(data.eur).toFixed(2) : (Number(compra) * 1.077).toFixed(2);
                  const ufv = data.ufv || '2.54';

                  // Elementos del Pill
                  const pillCompra = document.getElementById('bcb-pill-compra');
                  const pillVenta = document.getElementById('bcb-pill-venta');
                  if (pillCompra) pillCompra.textContent = compra;
                  if (pillVenta) pillVenta.textContent = venta;

                  // Elementos del Dropdown
                  const dropCompra = document.getElementById('bcb-compra-val');
                  const dropVenta = document.getElementById('bcb-venta-val');
                  if (dropCompra) dropCompra.textContent = compra;
                  if (dropVenta) dropVenta.textContent = venta;

                  const regText = document.getElementById('bcb-regimen-text');
                  if (regText) regText.textContent = regimen === 'FLEXIBLE' ? 'Flexible' : regimen;

                  const notaText = document.getElementById('bcb-nota-text');
                  if (notaText && data.nota) {
                      notaText.innerHTML = `<span class="font-semibold text-purple-300">Régimen Flexible (BCB):</span> ${data.nota.length > 130 ? data.nota.substring(0, 130) + '...' : data.nota}`;
                  }

                  const dropEur = document.getElementById('bcb-eur-val');
                  if (dropEur) dropEur.textContent = `${eur} Bs`;

                  const dropUfv = document.getElementById('bcb-ufv-val');
                  if (dropUfv) dropUfv.textContent = `${ufv} Bs`;

                  const lastUpd = document.getElementById('bcb-last-updated');
                  if (lastUpd) {
                      lastUpd.textContent = fecha ? `Actualizado: ${fecha}` : 'Actualizado en tiempo real';
                  }

                  // Actualizar secuencias del carrusel de tasas
                  rates[0] = `<span class="text-white font-bold">USD</span> <span class="text-purple-200 text-[10px] font-normal">C:</span><span class="text-emerald-300 font-bold font-mono text-[11px]">${compra}</span> <span class="text-white/40 font-light">/</span> <span class="text-purple-200 text-[10px] font-normal">V:</span><span class="text-emerald-300 font-bold font-mono text-[11px]">${venta}</span> <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse ml-0.5" title="En tiempo real"></span>`;
                  rates[1] = `<span class="text-emerald-300 font-bold">BCB</span> <span class="text-purple-200 text-[10px]">Régimen:</span> <span class="text-amber-300 font-bold text-[11px] uppercase">${regimen}</span>`;
                  rates[2] = `<span class="text-white font-bold">EUR</span> <span class="text-purple-200 text-[10px] font-normal">Ref:</span><span class="text-blue-300 font-bold font-mono text-[11px]">${eur} Bs</span>`;
                  rates[3] = `<span class="text-white font-bold">UFV</span> <span class="text-purple-200 text-[10px] font-normal">Hoy:</span><span class="text-amber-300 font-bold font-mono text-[11px]">${ufv} Bs</span>`;

                  if (currentRateIndex === 0) {
                      bcbTicker.innerHTML = rates[0];
                  }
              }

              async function loadBcbRealTimeRates(forceRefresh = false) {
                  const CACHE_KEY = 'uhtv_bcb_rates_v1';
                  const CACHE_TIME = 10 * 60 * 1000; // 10 minutos
                  const refreshBtn = document.getElementById('bcb-manual-refresh');

                  if (!forceRefresh) {
                      try {
                          const cached = sessionStorage.getItem(CACHE_KEY);
                          if (cached) {
                              const parsed = JSON.parse(cached);
                              if (Date.now() - parsed.time < CACHE_TIME && parsed.data) {
                                  updateBcbDOM(parsed.data);
                                  return;
                              }
                          }
                      } catch (e) {}
                  }

                  if (refreshBtn) refreshBtn.querySelector('i')?.classList.add('fa-spin');

                  let rateData = null;

                  // 1. API Principal: apibcb.cucu.bo
                  try {
                      const controller = new AbortController();
                      const timeout = setTimeout(() => controller.abort(), 4000);
                      const res = await fetch('https://apibcb.cucu.bo/api/v1/tc/oficial', {
                          signal: controller.signal,
                          headers: { 'Accept': 'application/json' }
                      });
                      clearTimeout(timeout);
                      if (res.ok) {
                          const json = await res.json();
                          if (json && json.tc_oficial) {
                              rateData = {
                                  compra: json.tc_oficial.compra || 11.0,
                                  venta: json.tc_oficial.venta || 11.1,
                                  regimen: json.tc_oficial.regimen || 'flexible',
                                  nota: json.tc_oficial.nota || 'Tipo de Cambio Oficial flexible (RD BCB 88/2026).',
                                  fecha: json.tc_oficial.fecha || '',
                                  eur: (json.tc_oficial.compra ? (json.tc_oficial.compra * 1.077).toFixed(2) : 11.85),
                                  ufv: '2.54'
                              };
                          }
                      }
                  } catch (err) {
                      console.warn('API CUCU no disponible, consultando respaldo...', err);
                  }

                  // 2. API de Respaldo: bo.dolarapi.com
                  if (!rateData) {
                      try {
                          const controller = new AbortController();
                          const timeout = setTimeout(() => controller.abort(), 4000);
                          const res = await fetch('https://bo.dolarapi.com/v1/dolares/oficial', {
                              signal: controller.signal,
                              headers: { 'Accept': 'application/json' }
                          });
                          clearTimeout(timeout);
                          if (res.ok) {
                              const json = await res.json();
                              if (json && (json.compra || json.venta)) {
                                  const c = json.compra || 11.0;
                                  const v = json.venta || (c + 0.1);
                                  rateData = {
                                      compra: c,
                                      venta: v,
                                      regimen: 'flexible',
                                      nota: 'Tipo de cambio oficial reportado por DolarAPI Bolivia.',
                                      fecha: json.fechaActualizacion ? json.fechaActualizacion.slice(0, 10) : '',
                                      eur: (c * 1.077).toFixed(2),
                                      ufv: '2.54'
                                  };
                              }
                          }
                      } catch (err) {
                          console.warn('API de respaldo también falló, usando datos base:', err);
                      }
                  }

                  if (refreshBtn) refreshBtn.querySelector('i')?.classList.remove('fa-spin');

                  if (rateData) {
                      try {
                          sessionStorage.setItem(CACHE_KEY, JSON.stringify({
                              time: Date.now(),
                              data: rateData
                          }));
                      } catch (e) {}
                      updateBcbDOM(rateData);
                  }
              }

              // Evento de refresco manual
              const refreshBtn = document.getElementById('bcb-manual-refresh');
              if (refreshBtn) {
                  refreshBtn.addEventListener('click', function(e) {
                      e.stopPropagation();
                      loadBcbRealTimeRates(true);
                  });
              }

              // Iniciar carga en segundo plano
              loadBcbRealTimeRates();
          }

          // 2. Lógica del Clima Rotativo Animado
          const weatherData = [
              { id: 'sc', name: 'Santa Cruz', temp: 28, code: 0, lat: -17.7863, lon: -63.1812 },
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
                      <i class="fas ${iconClass} text-xs sm:text-sm"></i>
                      <span class="font-bold text-white text-xs">${city.temp}°</span>
                      <span class="text-[11px] text-gray-200 font-medium truncate max-w-[85px] sm:max-w-none">- ${city.name}</span>
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
