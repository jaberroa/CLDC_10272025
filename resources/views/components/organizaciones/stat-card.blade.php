@props([
    'title' => 'Título',
    'value' => '0',
    'icon' => 'ri-bar-chart-line',
    'background' => 'bg-primary-subtle',
    'iconBackground' => 'bg-primary',
    'description' => null,
    'trend' => null,
    'trendValue' => null,
    'trendType' => 'up' // up, down, neutral
])

<div class="card overflow-hidden">
    <div class="card-body {{ $background }} position-relative z-1">
        <div class="d-flex gap-2">
            <div class="school-icon {{ $iconBackground }} d-flex justify-content-center align-items-center fs-4">
                <i class="{{ $icon }}" id="hexagon"></i>
            </div>
            <div class="text-center flex-grow-1">
                <span class="d-block fw-semibold mb-2 fs-5">{{ $title }}</span>
                <h4 class="mb-0 fw-semibold">{{ $value }}</h4>
                @if($description)
                    <small class="text-muted">{{ $description }}</small>
                @endif
                @if($trend && $trendValue)
                    <div class="d-flex align-items-center justify-content-center mt-1">
                        @if($trendType === 'up')
                            <i class="ri-arrow-up-line text-success me-1"></i>
                            <span class="text-success fs-12 fw-medium">+{{ $trendValue }}</span>
                        @elseif($trendType === 'down')
                            <i class="ri-arrow-down-line text-danger me-1"></i>
                            <span class="text-danger fs-12 fw-medium">-{{ $trendValue }}</span>
                        @else
                            <i class="ri-subtract-line text-muted me-1"></i>
                            <span class="text-muted fs-12 fw-medium">{{ $trendValue }}</span>
                        @endif
                        <small class="text-muted ms-1">{{ $trend }}</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

