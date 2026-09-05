@props([
    'variant' => 'sage', // sage, terracotta, info, warning, danger
    'class' => ''
])

@php
    $styles = match($variant) {
        'terracotta' => 'background-color: var(--color-terracotta-light); color: var(--color-terracotta);',
        'info' => 'background-color: #E6F0F8; color: var(--color-info);',
        'warning' => 'background-color: #FFF8E7; color: #B38324;',
        'danger' => 'background-color: #FDEAEA; color: var(--color-danger);',
        default => 'background-color: var(--color-light-sage); color: var(--color-dark-sage);'
    };
@endphp

<span {{ $attributes->merge(['class' => 'd-inline-flex align-items-center gap-1 px-25 py-1 rounded-pill fw-semibold ' . $class]) }} style="font-size: 0.75rem; padding: 0.25rem 0.65rem; {{ $styles }}">
    {{ $slot }}
</span>
