@props([
    'variant' => 'primary', // primary, secondary, danger, outline
    'type' => 'button',
    'icon' => null,
    'class' => ''
])

@php
    $variantClass = match($variant) {
        'secondary' => 'btn-m-secondary',
        'danger' => 'btn-m-danger',
        'outline' => 'btn-m-outline',
        default => 'btn-m-primary'
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $variantClass . ' ' . $class]) }}>
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    <span>{{ $slot }}</span>
</button>
