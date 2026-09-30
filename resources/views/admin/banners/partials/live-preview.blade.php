@php
    $uhtvLocations = \App\Services\BannerLocationService::all();
    $uhtvCurrent = old('location', $selected ?? '');
@endphp

<div class="card border-primary shadow-sm">
    <div class="card-header py-2 bg-gradient-primary text-white">
        <h6 class="m-0 small fw-bold">
            <i class="fas fa-eye me-2"></i>Dónde aparecerá este banner
        </h6>
    </div>
    <div class="card-body p-3">
        <div id="uhtvLivePreview">
            @foreach($uhtvLocations as $uhtvKey => $uhtvDef)
                <div class="uhtv-loc-preview mb-3" data-location="{{ $uhtvKey }}"
                     style="{{ $uhtvCurrent === $uhtvKey ? '' : 'display:none' }}">

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-{{ $uhtvDef['color'] }}">
                            <i class="fas {{ $uhtvDef['icon'] }} me-1"></i>{{ $uhtvDef['label'] }}
                        </span>
                        <code class="small">{{ $uhtvKey }}</code>
                    </div>

                    @include('admin.banners.partials.location-mockup', [
                        'location' => $uhtvKey,
                        'imageUrl' => $imageUrl ?? null,
                        'activeLabel' => 'AQUÍ',
                        'activeSize' => $uhtvDef['dimensions'],
                    ])
                    <p class="small text-muted mt-2 mb-1">{{ $uhtvDef['description'] }}</p>
                    <p class="small mb-1">
                        <strong>Tamaño recomendado:</strong> {{ $uhtvDef['dimensions'] }}
                    </p>
                    <p class="small mb-1">
                        <strong>Aparece en:</strong> {{ $uhtvDef['pages'] }}
                    </p>
                    <p class="small mb-0">
                        <strong>Archivo:</strong> <code class="small">{{ $uhtvDef['rendered_in'] }}</code>
                    </p>

                    @if(!empty($uhtvDef['notes']))
                        <div class="alert alert-warning py-2 px-3 mt-2 mb-0 small">
                            <i class="fas fa-exclamation-triangle me-1"></i>{{ $uhtvDef['notes'] }}
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="alert alert-secondary py-2 px-3 small mb-0" id="uhtvNoLocation"
                 style="{{ $uhtvCurrent ? 'display:none' : '' }}">
                <i class="fas fa-hand-pointer me-1"></i>
                Elige una <strong>ubicación</strong> y verás aquí un dibujo de la página
                con el hueco exacto donde se mostrará tu banner.
            </div>
        </div>
    </div>
</div>

@once
<style>
    .card-header.bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
</style>
@endonce

@push('scripts')
<script>
    (function () {
        const select = document.getElementById('location');
        const wrap = document.getElementById('uhtvLivePreview');
        if (!select || !wrap) return;

        const previews = wrap.querySelectorAll('.uhtv-loc-preview');
        const empty = document.getElementById('uhtvNoLocation');

        function show() {
            const value = select.value;

            previews.forEach(function (p) {
                p.style.display = p.getAttribute('data-location') === value ? '' : 'none';
            });

            if (empty) empty.style.display = value ? 'none' : '';
        }

        select.addEventListener('change', show);
        show();

        // Propaga la imagen seleccionada a todos los huecos del mockup.
        window.setBannerPreviewImage = function (src) {
            wrap.querySelectorAll('.uhtv-mm-slot-active').forEach(function (slot) {
                const img = slot.querySelector('.uhtv-mm-slot-img');
                const cap = slot.querySelector('.uhtv-mm-slot-cap');
                if (!img || !cap) return;

                if (src) {
                    img.src = src;
                    img.style.display = '';
                    cap.style.display = 'none';
                } else {
                    img.removeAttribute('src');
                    img.style.display = 'none';
                    cap.style.display = '';
                }
            });
        };
    })();
</script>
@endpush
