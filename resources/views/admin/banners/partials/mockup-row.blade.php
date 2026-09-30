@php
    $uhtvRowType = $row['type'] ?? 'content';
    $uhtvRowKey = $row['key'] ?? null;
    $uhtvIsBanner = in_array($uhtvRowType, ['banner', 'overlay'], true);
    $uhtvIsActive = $uhtvIsBanner && $uhtvRowKey === $activeKey;
    $uhtvRatio = \App\Services\BannerLocationService::mockupRatio($uhtvRowKey ?: $activeKey);
@endphp

@if($uhtvRowType === 'split')
    <div class="uhtv-mm-split">
        <div class="uhtv-mm-col">
            @foreach($row['main'] as $uhtvSubRow)
                @include('admin.banners.partials.mockup-row', [
                    'row' => $uhtvSubRow,
                    'activeKey' => $activeKey,
                    'imageUrl' => $imageUrl,
                    'activeLabel' => $activeLabel,
                    'activeSize' => $activeSize,
                ])
            @endforeach
        </div>
        <div class="uhtv-mm-col uhtv-mm-col-side">
            @foreach($row['side'] as $uhtvSubRow)
                @include('admin.banners.partials.mockup-row', [
                    'row' => $uhtvSubRow,
                    'activeKey' => $activeKey,
                    'imageUrl' => $imageUrl,
                    'activeLabel' => $activeLabel,
                    'activeSize' => $activeSize,
                ])
            @endforeach
        </div>
    </div>

@elseif($uhtvRowType === 'overlay')
    {{-- Popup: se dibuja por encima de la pagina entera --}}
    <div class="uhtv-mm-overlay">
        <span class="uhtv-mm-overlay-x"><i class="fas fa-times"></i></span>
        <div class="uhtv-mm-slot uhtv-mm-slot-active" style="aspect-ratio: {{ $uhtvRatio }};">
            <span class="uhtv-mm-slot-tag">TU BANNER</span>
            <img class="uhtv-mm-slot-img" src="{{ $imageUrl ?? '' }}" alt="Ubicación del banner"
                 style="{{ empty($imageUrl) ? 'display:none' : '' }}">
            <span class="uhtv-mm-slot-cap" style="{{ empty($imageUrl) ? '' : 'display:none' }}">
                {{ $activeLabel }}
                @if(!empty($activeSize))
                    <small>{{ $activeSize }}</small>
                @endif
            </span>
        </div>
    </div>

@elseif($uhtvIsActive)
    {{-- Hueco activo: con la imagen real del banner si se ha elegido una --}}
    <div class="uhtv-mm-slot uhtv-mm-slot-active" style="aspect-ratio: {{ $uhtvRatio }};">
        <span class="uhtv-mm-slot-tag">TU BANNER</span>
        <img class="uhtv-mm-slot-img" src="{{ $imageUrl ?? '' }}" alt="Ubicación del banner"
             style="{{ empty($imageUrl) ? 'display:none' : '' }}">
        <span class="uhtv-mm-slot-cap" style="{{ empty($imageUrl) ? '' : 'display:none' }}">
            {{ $activeLabel }}
            @if(!empty($activeSize))
                <small>{{ $activeSize }}</small>
            @endif
        </span>
    </div>

@elseif($uhtvRowType === 'banner')
    {{-- Otro espacio publicitario de la misma página --}}
    <div class="uhtv-mm-slot uhtv-mm-slot-idle" style="aspect-ratio: {{ $uhtvRatio }};">
        otro espacio publicitario
    </div>

@elseif($uhtvRowType === 'bar')
    <div class="uhtv-mm-row uhtv-mm-row-chrome">{{ $row['label'] ?? '' }}</div>

@else
    <div class="uhtv-mm-row uhtv-mm-row-content">{{ $row['label'] ?? '' }}</div>
@endif
