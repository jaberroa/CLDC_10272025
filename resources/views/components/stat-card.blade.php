@props([
    'title' => '',
    'value' => 0,
    'icon' => 'ri-bar-chart-line',
    'color' => 'primary',
    'subtitle' => '',
    'trend' => null,
    'trendValue' => null
])

<div class="card overflow-hidden">
    <div class="card-body bg-{{ $color }}-subtle position-relative z-1">
        <div class="d-flex gap-2">
            <div class="school-icon bg-{{ $color }} d-flex justify-content-center align-items-center fs-4">
                <i class="{{ $icon }}" id="hexagon"></i>
            </div>
            <div class="text-center">
                <span class="d-block fw-semibold mb-2 fs-5">{{ $title }}</span>
                <h4 class="mb-0 fw-semibold" data-stat-value="{{ $value }}">{{ number_format($value) }}</h4>
                @if($subtitle)
                    <small class="text-muted">{{ $subtitle }}</small>
                @endif
                @if($trend && $trendValue)
                    <div class="mt-1">
                        <span class="badge bg-{{ $trend === 'up' ? 'success' : 'danger' }} fs-12">
                            <i class="ri-arrow-{{ $trend === 'up' ? 'up' : 'down' }}-line"></i>
                            {{ $trendValue }}%
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

