  <!-- Header Principal -->
  <header class="relative bg-gradient-to-r from-purple-900 via-purple-700 to-red-700 dark:from-gray-950 dark:via-purple-900 dark:to-red-900 min-h-[110px] flex items-center justify-center text-white overflow-hidden py-4 px-4 transition-colors duration-300">
    
    <!-- Overlay de oscurecimiento para legibilidad premium -->
    <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/55 to-black/75 dark:from-black/85 dark:via-black/65 dark:to-black/85"></div>
    
    <!-- Elementos decorativos de fondo -->
    <div class="absolute inset-0 opacity-10">
      <div class="absolute top-4 left-4 w-32 h-32 bg-white rounded-full blur-3xl"></div>
      <div class="absolute bottom-4 right-4 w-24 h-24 bg-purple-300 rounded-full blur-2xl"></div>
    </div>

    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center gap-4 relative z-10">
    @include('partials.header.weather')
    @include('partials.header.logo')
    @include('partials.header.currency')
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
