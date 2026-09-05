@props([
    'variant' => 'info', // info, success, warning, danger
    'title' => null,
    'dismissible' => false,
    'icon' => null
])

@php
    $bg = match($variant) {
        'success' => 'background-color: var(--color-light-sage); border-color: #C3DFC7; color: var(--color-dark-sage);',
        'warning' => 'background-color: #FFF8E7; border-color: #F3E2B8; color: #8C6615;',
        'danger' => 'background-color: #FDEAEA; border-color: #F5C6C6; color: var(--color-danger);',
        default => 'background-color: #E6F0F8; border-color: #B9D6EE; color: var(--color-info);'
    };

    $defaultIcon = match($variant) {
        'success' => 'bi bi-check-circle-fill',
        'warning' => 'bi bi-exclamation-triangle-fill',
        'danger' => 'bi bi-exclamation-octagon-fill',
        default => 'bi bi-info-circle-fill'
    };

    $iconClass = $icon ?? $defaultIcon;
@endphp

<div {{ $attributes->merge(['class' => 'p-3 mb-3 rounded-3 border d-flex align-items-start gap-3']) }} style="{{ $bg }}">
    <div class="fs-5 lh-1 mt-1">
        <i class="{{ $iconClass }}"></i>
    </div>
    <div class="flex-grow-1">
        @if($title)
            <div class="fw-semibold mb-1" style="font-size: 0.95rem;">{{ $title }}</div>
        @endif
        <div style="font-size: 0.88rem;">
            {{ $slot }}
        </div>
    </div>
</div>
