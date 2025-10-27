@props(['estado'])

<div class="d-flex align-items-center estado-membresia-container">
    <div class="flex-shrink-0 me-2">
        @if($estado)
            @if($estado->nombre === 'activa')
                <div class="avatar-xs bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri-check-line text-success fs-10"></i>
                </div>
            @elseif($estado->nombre === 'suspendida')
                <div class="avatar-xs bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri-pause-line text-danger fs-10"></i>
                </div>
            @else
                <div class="avatar-xs bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center">
                    <i class="ri-time-line text-warning fs-10"></i>
                </div>
            @endif
        @else
            <div class="avatar-xs bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center">
                <i class="ri-question-line text-secondary fs-10"></i>
            </div>
        @endif
    </div>
    <div class="flex-grow-1">
        @if($estado)
            <span class="badge bg-{{ $estado->nombre === 'activa' ? 'success' : ($estado->nombre === 'suspendida' ? 'danger' : 'warning') }} bg-opacity-10 text-{{ $estado->nombre === 'activa' ? 'success' : ($estado->nombre === 'suspendida' ? 'danger' : 'warning') }} border border-{{ $estado->nombre === 'activa' ? 'success' : ($estado->nombre === 'suspendida' ? 'danger' : 'warning') }} border-opacity-25 fw-semibold">
                {{ ucfirst($estado->nombre) }}
            </span>
        @else
            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 fw-semibold">
                Sin Estado
            </span>
        @endif
    </div>
</div>
