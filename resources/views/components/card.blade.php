@props([
    'title' => null,
    'subtitle' => null,
    'action' => null,
    'hoverable' => false,
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'm-card ' . ($hoverable ? 'hoverable ' : '') . $class]) }}>
    @if($title || $subtitle || $action)
        <div class="m-card-header">
            <div>
                @if($title)
                    <h3 class="m-card-title">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="m-card-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            @if($action)
                <div>
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>
