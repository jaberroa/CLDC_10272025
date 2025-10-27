@props(['miembro', 'estadisticas'])

<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Proyectos Activos</h5>
        <div class="mb-5 pb-5 border-bottom">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Participación en Asambleas</h6>
                <span class="badge bg-primary">En Progreso</span>
            </div>
            <p class="text-muted mb-3">Inicio: <span class="text-body">{{ $miembro->fecha_ingreso->format('M d, Y') }}</span> • Estado: <span class="text-body">Activo</span></p>
            <p>Participación activa en las asambleas generales y extraordinarias de la organización, contribuyendo al proceso democrático y la toma de decisiones.</p>
            <div class="d-flex justify-content-between align-items-center mb-1 fs-13">
                <p class="mb-0 text-muted">Asambleas: <span class="text-body">{{ $estadisticas['asambleas_asistidas'] }}/{{ $estadisticas['asambleas_totales'] ?? 1 }}</span></p>
                <p class="text-muted mb-0">{{ round(($estadisticas['asambleas_asistidas'] / max($estadisticas['asambleas_totales'] ?? 1, 1)) * 100) }}% Completado</p>
            </div>
            <div class="progress progress-sm">
                <div class="progress-bar bg-primary" style="width: {{ round(($estadisticas['asambleas_asistidas'] / max($estadisticas['asambleas_totales'] ?? 1, 1)) * 100) }}%;" role="progressbar"></div>
            </div>
        </div>
        <div class="mb-5 pb-5 border-bottom">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Programa de Capacitaciones</h6>
                <span class="badge bg-success">En Progreso</span>
            </div>
            <p class="text-muted mb-3">Inicio: <span class="text-body">{{ $miembro->fecha_ingreso->format('M d, Y') }}</span> • Estado: <span class="text-body">Activo</span></p>
            <p>Participación en programas de formación y desarrollo profesional ofrecidos por la organización para el crecimiento personal y profesional.</p>
            <div class="d-flex justify-content-between align-items-center mb-1 fs-13">
                <p class="mb-0 text-muted">Capacitaciones: <span class="text-body">{{ $estadisticas['capacitaciones_inscrito'] }}/{{ $estadisticas['capacitaciones_totales'] ?? 1 }}</span></p>
                <p class="text-muted mb-0">{{ round(($estadisticas['capacitaciones_inscrito'] / max($estadisticas['capacitaciones_totales'] ?? 1, 1)) * 100) }}% Completado</p>
            </div>
            <div class="progress progress-sm">
                <div class="progress-bar bg-success" style="width: {{ round(($estadisticas['capacitaciones_inscrito'] / max($estadisticas['capacitaciones_totales'] ?? 1, 1)) * 100) }}%;" role="progressbar"></div>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Procesos Electorales</h6>
                <span class="badge bg-info">Participante</span>
            </div>
            <p class="text-muted mb-3">Inicio: <span class="text-body">{{ $miembro->fecha_ingreso->format('M d, Y') }}</span> • Estado: <span class="text-body">Activo</span></p>
            <p>Ejercicio responsable del derecho al voto en los procesos democráticos de la organización, contribuyendo a la gobernanza institucional.</p>
            <div class="d-flex justify-content-between align-items-center mb-1 fs-13">
                <p class="mb-0 text-muted">Elecciones: <span class="text-body">{{ $estadisticas['elecciones_participado'] }}/{{ $estadisticas['elecciones_totales'] ?? 1 }}</span></p>
                <p class="text-muted mb-0">{{ round(($estadisticas['elecciones_participado'] / max($estadisticas['elecciones_totales'] ?? 1, 1)) * 100) }}% Completado</p>
            </div>
            <div class="progress progress-sm">
                <div class="progress-bar bg-info" style="width: {{ round(($estadisticas['elecciones_participado'] / max($estadisticas['elecciones_totales'] ?? 1, 1)) * 100) }}%;" role="progressbar"></div>
            </div>
        </div>
    </div>
</div>


