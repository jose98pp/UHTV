      <!-- Right Column: BCB Currency Exchange + Live Badge + Dark Mode Toggle -->
      <div class="flex items-center justify-end space-x-3 w-full md:w-1/3">
        <!-- Widget Cotización BCB (Tipo de Cambio Bolivia en Tiempo Real) -->
        <div class="relative" id="widget-tipo-cambio-bcb">
          <div id="bcb-pill-btn" class="flex items-center gap-2 bg-black/40 hover:bg-black/60 backdrop-blur-md border border-purple-400/30 hover:border-purple-300 px-3 py-1.5 rounded-full text-white shadow-md transition-all duration-300 cursor-pointer select-none group" title="Ver cotización oficial del BCB en tiempo real">
            <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded text-[10px] font-black bg-gradient-to-r from-purple-600 to-indigo-600 text-white border border-purple-400/40 tracking-wider shadow-sm">
              BCB
            </span>
            <div id="bcb-ticker-display" class="flex items-center gap-1.5 text-xs font-semibold tracking-tight transition-all duration-300">
              <span class="text-white font-bold">USD</span>
              <span class="text-purple-200 text-[10px] font-normal">C:</span><span id="bcb-pill-compra" class="text-emerald-300 font-bold font-mono text-[11px]">11.00</span>
              <span class="text-white/40 font-light">/</span>
              <span class="text-purple-200 text-[10px] font-normal">V:</span><span id="bcb-pill-venta" class="text-emerald-300 font-bold font-mono text-[11px]">11.10</span>
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse ml-0.5" title="En tiempo real"></span>
            </div>
            <i id="bcb-chevron-icon" class="fas fa-chevron-down text-[8px] text-purple-200 group-hover:text-white transition-transform duration-200"></i>
          </div>

          <!-- Dropdown con cotizaciones oficiales BCB en tiempo real -->
          <div id="bcb-dropdown-panel" class="absolute right-0 top-full mt-2 w-72 sm:w-80 bg-gray-900/95 dark:bg-gray-950/95 backdrop-blur-xl border border-purple-500/30 rounded-2xl shadow-2xl p-3.5 z-50 hidden opacity-0 transition-all duration-200 text-white">
            <div class="flex items-center justify-between border-b border-purple-500/20 pb-2 mb-2.5">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center text-white shadow-inner">
                  <i class="fas fa-landmark text-xs"></i>
                </div>
                <div>
                  <span class="font-bold text-xs uppercase tracking-wide text-white block leading-tight">Cotización Oficial</span>
                  <span class="text-[9px] text-purple-300 font-medium">Banco Central de Bolivia</span>
                </div>
              </div>
              <span id="bcb-regimen-badge" class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-semibold border border-emerald-500/30 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span id="bcb-regimen-text">Flexible</span>
              </span>
            </div>

            <div class="space-y-2 text-xs">
              <!-- Dólar Estadounidense - Tarjeta Destacada -->
              <div class="rounded-xl bg-gradient-to-br from-purple-950/60 via-gray-900/90 to-indigo-950/60 border border-purple-500/30 p-2.5 shadow-md">
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-xs font-bold border border-emerald-500/30">$</span>
                    <div>
                      <div class="font-bold text-white text-xs leading-none">Dólar Estadounidense</div>
                      <div class="text-[10px] text-purple-200/70 leading-tight">USD / BOB</div>
                    </div>
                  </div>
                  <span class="text-[9px] bg-purple-600/30 text-purple-200 border border-purple-400/30 px-2 py-0.5 rounded font-mono font-bold uppercase tracking-wider">Oficial BCB</span>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1.5 border-t border-white/5">
                  <div class="bg-black/30 rounded-lg p-2 text-center border border-white/5">
                    <div class="text-[10px] text-purple-200/80 uppercase font-semibold">Compra (TCO)</div>
                    <div class="font-mono text-base font-extrabold text-emerald-300 tracking-tight">
                      <span id="bcb-compra-val">11.00</span> <span class="text-xs text-gray-300 font-normal">Bs</span>
                    </div>
                  </div>
                  <div class="bg-black/30 rounded-lg p-2 text-center border border-white/5">
                    <div class="text-[10px] text-purple-200/80 uppercase font-semibold">Venta (Tope)</div>
                    <div class="font-mono text-base font-extrabold text-emerald-300 tracking-tight">
                      <span id="bcb-venta-val">11.10</span> <span class="text-xs text-gray-300 font-normal">Bs</span>
                    </div>
                  </div>
                </div>

                <div id="bcb-nota-box" class="mt-2 text-[10px] text-purple-200/90 bg-white/5 rounded-md p-1.5 leading-snug border border-white/5">
                  <i class="fas fa-info-circle text-purple-400 mr-1"></i>
                  <span id="bcb-nota-text">Régimen flexible según RD BCB 88/2026. Promedio ponderado diario oficial.</span>
                </div>
              </div>

              <!-- Referenciales: Euro y UFV -->
              <div class="grid grid-cols-2 gap-2">
                <!-- Euro -->
                <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10 hover:border-blue-500/30 transition-all">
                  <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-300 flex items-center justify-center text-[10px] font-bold">€</span>
                    <div>
                      <div class="font-bold text-white text-[11px] leading-none">Euro</div>
                      <div class="text-[9px] text-gray-400">Referencial</div>
                    </div>
                  </div>
                  <div class="text-right font-mono text-[11px] text-blue-300 font-bold" id="bcb-eur-val">
                    11.85 Bs
                  </div>
                </div>

                <!-- UFV -->
                <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10 hover:border-amber-500/30 transition-all">
                  <div class="flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 flex items-center justify-center text-[10px] font-bold">U</span>
                    <div>
                      <div class="font-bold text-white text-[11px] leading-none">UFV</div>
                      <div class="text-[9px] text-gray-400">Vivienda</div>
                    </div>
                  </div>
                  <div class="text-right font-mono text-[11px] text-amber-300 font-bold" id="bcb-ufv-val">
                    2.54 Bs
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer con actualización en tiempo real -->
            <div class="mt-2.5 pt-2 border-t border-purple-500/20 text-[9px] text-gray-400 flex items-center justify-between">
              <div class="flex items-center gap-1.5">
                <button type="button" id="bcb-manual-refresh" class="text-purple-300 hover:text-white transition-colors cursor-pointer" title="Actualizar en tiempo real">
                  <i class="fas fa-sync-alt text-[10px]"></i>
                </button>
                <span id="bcb-last-updated">Actualizado en tiempo real</span>
              </div>
              <a href="https://www.bcb.gob.bo" target="_blank" rel="noopener noreferrer" class="text-purple-300 hover:text-white transition-colors flex items-center gap-1">
                <span>bcb.gob.bo</span>
                <i class="fas fa-external-link-alt text-[8px]"></i>
              </a>
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
