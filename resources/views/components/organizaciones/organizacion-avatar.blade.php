@props(['organizacion'])

<div class="d-flex align-items-center">
    <div class="flex-shrink-0 me-3">
        @if($organizacion->logo_url)
            <img src="{{ $organizacion->logo_url }}" 
                 alt="{{ $organizacion->nombre }}" 
                 class="rounded-circle" 
                 style="width: 40px; height: 40px; object-fit: cover;">
        @else
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" 
                 style="width: 40px; height: 40px;">
                <i class="ri-building-line"></i>
            </div>
        @endif
    </div>
    <div class="flex-grow-1">
        <h6 class="mb-0 fw-semibold">{{ $organizacion->nombre }}</h6>
        <small class="text-muted">{{ $organizacion->codigo }}</small>
    </div>
</div>

