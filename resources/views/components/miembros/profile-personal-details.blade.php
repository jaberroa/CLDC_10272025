@props(['miembro'])

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Detalles Personales</h5>
        <a href="{{ route('miembros.edit', $miembro->id) }}" class="fs-14"><i class="ri-edit-line me-1"></i>Editar</a>
    </div>
    <div class="card-body d-flex flex-column gap-4 text-truncate">
        <div class="d-flex align-items-center gap-3">
            <i class="ri-map-pin-line fs-16 text-muted"></i>
            <p class="mb-0">{{ $miembro->organizacion->nombre }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <i class="ri-mail-line fs-16 text-muted"></i>
            <p class="mb-0">{{ $miembro->email }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <i class="ri-phone-line fs-16 text-muted"></i>
            <p class="mb-0">{{ $miembro->telefono ?? 'No especificado' }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <i class="ri-shield-user-line fs-16 text-muted"></i>
            <p class="mb-0">{{ ucfirst($miembro->tipo_membresia) }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <i class="ri-user-2-line fs-16 text-muted"></i>
            <p class="mb-0">{{ ucfirst($miembro->estado_membresia) }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <i class="ri-calendar-line fs-16 text-muted"></i>
            <p class="mb-0">{{ $miembro->fecha_ingreso->diffForHumans() }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <i class="ri-time-line fs-16 text-muted"></i>
            <p class="mb-0">Miembro desde {{ $miembro->fecha_ingreso->format('M d, Y') }}</p>
        </div>
    </div>
</div>


