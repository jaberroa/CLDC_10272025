@props(['miembro', 'actividadReciente', 'asambleasHistorial', 'capacitacionesHistorial'])

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Actividad Reciente</h5>
            </div>
            <div class="card-body">
                <div class="timeline2 icon-timeline">
                    <ul>
                        @foreach($actividadReciente as $actividad)
                        <li class="box">
                            <span class="bg-{{ $actividad['color'] }}">
                                <i class="{{ $actividad['icono'] }}"></i>
                            </span>
                            <div class="text-muted float-end fs-13">{{ $actividad['fecha']->format('d M Y') }}</div>
                            <div class="title">{{ $actividad['titulo'] }}</div>
                            <div class="info">{{ $actividad['descripcion'] }}</div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Historial de Asambleas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Asamblea</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Asistencia</th>
                                <th>Modalidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asambleasHistorial ?? [] as $asamblea)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2">
                                            <i class="ri-calendar-line fs-16"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $asamblea['titulo'] ?? 'Asamblea General' }}</h6>
                                            <small class="text-muted">{{ $asamblea['descripcion'] ?? 'Descripción no disponible' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $asamblea['fecha'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $asamblea['tipo'] == 'ordinaria' ? 'primary' : ($asamblea['tipo'] == 'extraordinaria' ? 'warning' : 'info') }}">
                                        {{ ucfirst($asamblea['tipo'] ?? 'ordinaria') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $asamblea['presente'] ? 'success' : 'secondary' }}">
                                        {{ $asamblea['presente'] ? 'Presente' : 'Ausente' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $asamblea['modalidad'] == 'presencial' ? 'primary' : 'info' }}">
                                        {{ ucfirst($asamblea['modalidad'] ?? 'presencial') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ri-calendar-line fs-1 mb-3 d-block"></i>
                                        <h5>Sin historial de asambleas</h5>
                                        <p>No hay registros de participación en asambleas.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Historial de Capacitaciones</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Capacitación</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Modalidad</th>
                                <th>Certificado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($capacitacionesHistorial ?? [] as $capacitacion)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-2">
                                            <i class="ri-graduation-cap-line fs-16"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $capacitacion['titulo'] ?? 'Capacitación' }}</h6>
                                            <small class="text-muted">{{ $capacitacion['descripcion'] ?? 'Descripción no disponible' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $capacitacion['fecha'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $capacitacion['estado'] == 'completada' ? 'success' : ($capacitacion['estado'] == 'en_progreso' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($capacitacion['estado'] ?? 'pendiente') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $capacitacion['modalidad'] == 'presencial' ? 'primary' : 'info' }}">
                                        {{ ucfirst($capacitacion['modalidad'] ?? 'presencial') }}
                                    </span>
                                </td>
                                <td>
                                    @if($capacitacion['certificado'] ?? false)
                                        <span class="badge bg-success">Disponible</span>
                                    @else
                                        <span class="badge bg-secondary">No disponible</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ri-graduation-cap-line fs-1 mb-3 d-block"></i>
                                        <h5>Sin historial de capacitaciones</h5>
                                        <p>No hay registros de participación en capacitaciones.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


