@php
    $uhtvDef = \App\Services\BannerLocationService::get($locationKey);
@endphp

@if($uhtvDef)
    <div class="card h-100 border-{{ $uhtvDef['color'] }} shadow-sm">
        <div class="card-header py-2 bg-{{ $uhtvDef['color'] }} text-white">
            <h6 class="mb-0 small fw-bold">
                <i class="fas {{ $uhtvDef['icon'] }} me-2"></i>{{ $uhtvDef['label'] }}
            </h6>
        </div>
        <div class="card-body p-3">
            @include('admin.banners.partials.location-mockup', ['location' => $locationKey])

            <div class="mt-3">
                <div class="mb-2">
                    <span class="badge bg-light text-dark border"><code>{{ $locationKey }}</code></span>
                    <span class="badge bg-secondary">{{ $uhtvDef['dimensions'] }}</span>
                </div>
                <p class="small text-muted mb-2">{{ $uhtvDef['description'] }}</p>
                <p class="small mb-2">
                    <i class="fas fa-sitemap me-1 text-secondary"></i>
                    <strong>Aparece en:</strong> {{ $uhtvDef['pages'] }}
                </p>
                <p class="small mb-0">
                    <i class="fas fa-code me-1 text-secondary"></i>
                    <strong>Se inserta en:</strong>
                    <code class="small">{{ $uhtvDef['rendered_in'] }}</code>
                </p>
            </div>

            @if(!empty($uhtvDef['notes']))
                <div class="alert alert-warning py-2 px-3 mt-3 mb-0 small">
                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $uhtvDef['notes'] }}
                </div>
            @endif
        </div>
    </div>
@endif
