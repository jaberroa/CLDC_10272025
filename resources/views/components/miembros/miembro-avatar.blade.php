@props(['miembro'])

<div class="d-flex align-items-center">
    <div class="flex-shrink-0">
        @if($miembro->foto_url)
            <img src="{{ asset('storage/' . $miembro->foto_url) }}" 
                 alt="{{ $miembro->nombre_completo }}" 
                 class="avatar-xs rounded-circle">
        @else
            <div class="avatar-xs rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                <i class="ri-user-line fs-12"></i>
            </div>
        @endif
    </div>
    <div class="flex-grow-1 ms-2">
        <h6 class="mb-0">{{ $miembro->nombre_completo }}</h6>
        <p class="text-muted mb-0 fs-12">
            <i class="ri-mail-line me-1"></i>{{ $miembro->email }}
        </p>
        <p class="text-muted mb-0 fs-12">
            <i class="ri-phone-line me-1"></i>{{ $miembro->telefono ?? 'Sin teléfono' }}
        </p>
        <p class="text-muted mb-0 fs-12">
            <i class="ri-id-card-line me-1"></i>{{ $miembro->numero_carnet }}
        </p>
    </div>
</div>
