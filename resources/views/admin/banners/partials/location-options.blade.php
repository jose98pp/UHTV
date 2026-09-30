@php
    $uhtvSelected = old('location', $selected ?? '');
@endphp

<option value="">Seleccionar ubicación...</option>
@foreach(\App\Services\BannerLocationService::all() as $uhtvKey => $uhtvItem)
    <option value="{{ $uhtvKey }}" {{ $uhtvSelected === $uhtvKey ? 'selected' : '' }}>
        {{ $uhtvItem['select_label'] }} — {{ $uhtvItem['dimensions'] }}
    </option>
@endforeach
