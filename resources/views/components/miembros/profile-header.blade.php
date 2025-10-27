@props(['miembro', 'estadisticas'])

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="avatar-xl position-relative">
                    @if($miembro->foto_url)
                        <img src="{{ asset('storage/' . $miembro->foto_url) }}" 
                             alt="{{ $miembro->nombre_completo }}" 
                             class="avatar-xl rounded-circle">
                    @else
                        <div class="avatar-xl rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                            <i class="ri-user-line fs-24"></i>
                        </div>
                    @endif
                    <span class="position-absolute profile-dot bg-success rounded-circle">
                        <span class="visually-hidden">miembro activo</span>
                    </span>
                </div>
            </div>
            <div class="flex-grow-1">
                <h4 class="mb-1">
                    {{ $miembro->nombre }} {{ $miembro->apellido }}
                    @if($miembro->tipo_membresia == 'fundador')
                        <i class="bi bi-patch-check-fill fs-16 ms-1 text-warning"></i>
                    @elseif($miembro->tipo_membresia == 'activo')
                        <i class="bi bi-patch-check-fill fs-16 ms-1 text-success"></i>
                    @endif
                </h4>
                <p class="text-muted mb-1">{{ ucfirst($miembro->tipo_membresia) }} - {{ $miembro->profesion ?? 'Profesional' }}</p>
                <p class="text-muted mb-0">{{ $miembro->organizacion->nombre }}</p>
            </div>
            <div class="d-flex flex-wrap gap-4">
                <div class="d-flex flex-column justify-content-center gap-1 w-128px text-center py-4 border rounded-2">
                    <h4 class="mb-2 lh-1">{{ $estadisticas['asambleas_asistidas'] }}</h4>
                    <span class="text-muted lh-sm fs-12">Asambleas</span>
                </div>
                <div class="d-flex flex-column justify-content-center gap-1 w-128px text-center py-4 border rounded-2">
                    <h4 class="mb-2 lh-1">{{ $estadisticas['capacitaciones_inscrito'] }}</h4>
                    <span class="text-muted lh-sm fs-12">Capacitaciones</span>
                </div>
                <div class="d-flex flex-column justify-content-center gap-1 w-128px text-center py-4 border rounded-2">
                    <h4 class="mb-2 lh-1">{{ $estadisticas['años_membresia'] }}</h4>
                    <span class="text-muted lh-sm fs-12">Años Miembro</span>
                </div>
            </div>
            <div class="d-flex float-end gap-2 flex-shrink-0">
                <a href="{{ route('miembros.carnet', $miembro->id) }}" class="btn btn-light">
                    <i class="ri-qr-code-line me-1"></i> Carnet Digital
                </a>
                <a href="{{ route('miembros.index') }}" class="btn btn-primary">
                    <i class="ri-arrow-left-line me-1"></i> Volver a Miembros
                </a>
            </div>
        </div>
    </div>
</div>


