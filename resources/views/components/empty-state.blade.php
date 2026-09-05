@props([
    'icon' => 'bi bi-folder2-open',
    'title' => 'No Data Found',
    'description' => 'There are no records available at the moment.',
    'actionText' => null,
    'actionUrl' => null,
    'actionIcon' => 'bi bi-plus-lg'
])

<div {{ $attributes->merge(['class' => 'm-empty-state']) }}>
    <div class="m-empty-icon">
        <i class="{{ $icon }}"></i>
    </div>
    <h4 class="m-empty-title">{{ $title }}</h4>
    <p class="m-empty-desc">{{ $description }}</p>
    
    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn-m-primary">
            @if($actionIcon)
                <i class="{{ $actionIcon }}"></i>
            @endif
            <span>{{ $actionText }}</span>
        </a>
    @elseif($slot->isNotEmpty())
        <div>
            {{ $slot }}
        </div>
    @endif
</div>
