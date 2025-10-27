@props(['miembro', 'estadisticas'])

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Resumen de Participación</h5>
    </div>
    <div class="card-body d-flex flex-column gap-4">
        <div>
            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                <span class="text-truncate"><i class="ri-calendar-line me-2 text-muted"></i>Asambleas</span>
                <span class="text-muted">{{ round(($estadisticas['asambleas_asistidas'] / max($estadisticas['asambleas_totales'] ?? 1, 1)) * 100) }}%</span>
            </div>
            <div class="progress progress-sm" role="progressbar" aria-label="Asambleas" aria-valuenow="{{ $estadisticas['asambleas_asistidas'] }}" aria-valuemin="0" aria-valuemax="{{ $estadisticas['asambleas_totales'] ?? 1 }}">
                <div class="progress-bar" style="width: {{ round(($estadisticas['asambleas_asistidas'] / max($estadisticas['asambleas_totales'] ?? 1, 1)) * 100) }}%"></div>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                <span class="text-truncate"><i class="ri-graduation-cap-line me-2 text-muted"></i>Capacitaciones</span>
                <span class="text-muted">{{ round(($estadisticas['capacitaciones_inscrito'] / max($estadisticas['capacitaciones_totales'] ?? 1, 1)) * 100) }}%</span>
            </div>
            <div class="progress progress-sm" role="progressbar" aria-label="Capacitaciones" aria-valuenow="{{ $estadisticas['capacitaciones_inscrito'] }}" aria-valuemin="0" aria-valuemax="{{ $estadisticas['capacitaciones_totales'] ?? 1 }}">
                <div class="progress-bar bg-success" style="width: {{ round(($estadisticas['capacitaciones_inscrito'] / max($estadisticas['capacitaciones_totales'] ?? 1, 1)) * 100) }}%"></div>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                <span class="text-truncate"><i class="ri-vote-line me-2 text-muted"></i>Elecciones</span>
                <span class="text-muted">{{ round(($estadisticas['elecciones_participado'] / max($estadisticas['elecciones_totales'] ?? 1, 1)) * 100) }}%</span>
            </div>
            <div class="progress progress-sm" role="progressbar" aria-label="Elecciones" aria-valuenow="{{ $estadisticas['elecciones_participado'] }}" aria-valuemin="0" aria-valuemax="{{ $estadisticas['elecciones_totales'] ?? 1 }}">
                <div class="progress-bar bg-warning" style="width: {{ round(($estadisticas['elecciones_participado'] / max($estadisticas['elecciones_totales'] ?? 1, 1)) * 100) }}%"></div>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                <span class="text-truncate"><i class="ri-award-line me-2 text-muted"></i>Antigüedad</span>
                <span class="text-muted">{{ $estadisticas['anos_miembro'] ?? 0 }} años</span>
            </div>
            <div class="progress progress-sm" role="progressbar" aria-label="Antigüedad" aria-valuenow="{{ $estadisticas['anos_miembro'] ?? 0 }}" aria-valuemin="0" aria-valuemax="10">
                <div class="progress-bar bg-danger" style="width: {{ min(($estadisticas['anos_miembro'] ?? 0) * 10, 100) }}%"></div>
            </div>
        </div>
    </div>
</div>


