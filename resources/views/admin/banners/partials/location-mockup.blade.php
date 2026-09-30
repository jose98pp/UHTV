@php
    $uhtvLocDefinition = \App\Services\BannerLocationService::get($location);
    $uhtvLocRows = $uhtvLocDefinition['mockup'] ?? [];
    $uhtvLocHasOverlay = collect($uhtvLocRows)->contains(fn ($r) => ($r['type'] ?? null) === 'overlay');
@endphp

@once
<style>
    .uhtv-mm {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
    }

    .uhtv-mm-chrome {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        background: #e2e8f0;
        border-bottom: 1px solid #cbd5e1;
    }

    .uhtv-mm-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .uhtv-mm-url {
        flex: 1;
        margin-left: 6px;
        font-size: 10px;
        color: #64748b;
        background: #fff;
        border-radius: 20px;
        padding: 2px 10px;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    }

    .uhtv-mm-body {
        position: relative;
        padding: 8px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        background: #f8fafc;
    }

    .uhtv-mm-row {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        border-radius: 5px;
        font-size: 9.5px;
        line-height: 1.2;
        padding: 5px 6px;
        font-weight: 600;
        min-height: 20px;
    }

    .uhtv-mm-row-chrome {
        background: #334155;
        color: #e2e8f0;
    }

    .uhtv-mm-row-content {
        background: #e2e8f0;
        color: #64748b;
        border: 1px solid #cbd5e1;
        border-style: dashed;
        font-weight: 500;
        min-height: 26px;
    }

    /* Huecos publicitarios */
    .uhtv-mm-slot {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        border-radius: 5px;
        overflow: hidden;
        min-height: 22px;
    }

    .uhtv-mm-slot-idle {
        background: #f1f5f9;
        border: 1px dashed #cbd5e1;
        color: #94a3b8;
        font-size: 9px;
        font-weight: 600;
        padding: 5px 6px;
    }

    .uhtv-mm-slot-active {
        background: #f5f3ff;
        border: 2px dashed #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }

    .uhtv-mm-slot-active img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .uhtv-mm-slot-tag {
        position: absolute;
        top: 0;
        left: 0;
        background: #7c3aed;
        color: #fff;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .04em;
        padding: 1px 6px;
        border-bottom-right-radius: 5px;
        z-index: 2;
    }

    .uhtv-mm-slot-cap {
        color: #7c3aed;
        font-size: 9px;
        font-weight: 700;
        padding: 4px 6px;
    }

    .uhtv-mm-slot-cap small {
        display: block;
        color: #8b5cf6;
        font-weight: 600;
        font-size: 8px;
    }

    /* Columnas */
    .uhtv-mm-split {
        display: grid;
        grid-template-columns: 1fr 78px;
        gap: 5px;
        align-items: start;
    }

    .uhtv-mm-col {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .uhtv-mm-col-side {
        border-left: 1px dashed #cbd5e1;
        padding-left: 5px;
    }

    /* Popup */
    .uhtv-mm-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, .72);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        z-index: 3;
    }

    .uhtv-mm-overlay .uhtv-mm-slot {
        width: 62px;
        aspect-ratio: 1;
    }

    .uhtv-mm-overlay-x {
        position: absolute;
        top: 8px;
        right: 10px;
        color: #ef4444;
        font-size: 12px;
        line-height: 1;
    }

    .uhtv-mm-hint {
        font-size: 10px;
        color: #64748b;
        text-align: center;
        margin-top: 6px;
    }
</style>
@endonce

<div class="uhtv-mm">
    <div class="uhtv-mm-chrome">
        <span class="uhtv-mm-dot" style="background:#f87171"></span>
        <span class="uhtv-mm-dot" style="background:#fbbf24"></span>
        <span class="uhtv-mm-dot" style="background:#34d399"></span>
        <span class="uhtv-mm-url">uhtv.com{{ \App\Services\BannerLocationService::mockupUrl($location) }}</span>
    </div>

    <div class="uhtv-mm-body">
        @foreach($uhtvLocRows as $uhtvLocRow)
            @if(($uhtvLocRow['type'] ?? null) === 'overlay')
                @continue
            @endif

            @include('admin.banners.partials.mockup-row', [
                'row' => $uhtvLocRow,
                'activeKey' => $location,
                'imageUrl' => $imageUrl ?? null,
                'activeLabel' => $activeLabel ?? 'TU BANNER AQUÍ',
                'activeSize' => $activeSize ?? null,
            ])
        @endforeach

        @if($uhtvLocHasOverlay)
            @include('admin.banners.partials.mockup-row', [
                'row' => ['type' => 'overlay', 'key' => $location],
                'activeKey' => $location,
                'imageUrl' => $imageUrl ?? null,
                'activeLabel' => $activeLabel ?? 'TU BANNER AQUÍ',
                'activeSize' => $activeSize ?? null,
            ])
        @endif
    </div>
</div>
