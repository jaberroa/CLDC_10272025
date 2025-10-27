@props(['miembro', 'estadisticas'])

<div class="d-flex flex-column gap-4">
    <!-- Información Personal -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Información Personal</h5>
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
                <p class="mb-0">{{ $estadisticas['años_membresia'] }} años de membresía</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <i class="ri-time-line fs-16 text-muted"></i>
                <p class="mb-0">Miembro desde {{ $miembro->fecha_ingreso->format('M d, Y') }}</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <i class="ri-id-card-line fs-16 text-muted"></i>
                <p class="mb-0">{{ $miembro->numero_carnet }}</p>
            </div>
        </div>
    </div>
    
    <!-- Participación CLDCI -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="ri-bar-chart-line me-2 text-success"></i>
                Participación CLDCI
            </h5>
        </div>
        <div class="card-body d-flex flex-column gap-4">
            <div>
                <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                    <span class="text-truncate"><i class="ri-calendar-line me-2 text-muted"></i>Asambleas</span>
                    <span class="text-muted">{{ $estadisticas['asambleas_asistidas'] }}</span>
                </div>
                <div class="progress progress-sm" role="progressbar" aria-label="Asambleas" aria-valuenow="{{ $estadisticas['asambleas_asistidas'] }}" aria-valuemin="0" aria-valuemax="10">
                    <div class="progress-bar" style="width: {{ min(($estadisticas['asambleas_asistidas'] / 10) * 100, 100) }}%"></div>
                </div>
            </div>
            <div>
                <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                    <span class="text-truncate"><i class="ri-graduation-cap-line me-2 text-muted"></i>Capacitaciones</span>
                    <span class="text-muted">{{ $estadisticas['capacitaciones_inscrito'] }}</span>
                </div>
                <div class="progress progress-sm" role="progressbar" aria-label="Capacitaciones" aria-valuenow="{{ $estadisticas['capacitaciones_inscrito'] }}" aria-valuemin="0" aria-valuemax="5">
                    <div class="progress-bar bg-success" style="width: {{ min(($estadisticas['capacitaciones_inscrito'] / 5) * 100, 100) }}%"></div>
                </div>
            </div>
            <div>
                <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                    <span class="text-truncate"><i class="ri-vote-line me-2 text-muted"></i>Elecciones</span>
                    <span class="text-muted">{{ $estadisticas['elecciones_participado'] }}</span>
                </div>
                <div class="progress progress-sm" role="progressbar" aria-label="Elecciones" aria-valuenow="{{ $estadisticas['elecciones_participado'] }}" aria-valuemin="0" aria-valuemax="3">
                    <div class="progress-bar bg-info" style="width: {{ min(($estadisticas['elecciones_participado'] / 3) * 100, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
