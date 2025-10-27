@props(['elecciones'])

<div class="table-responsive">
    <table class="table table-hover text-nowrap elecciones-table">
        <thead class="table-light">
            <tr>
                <th>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        <label class="form-check-label" for="selectAll">
                            <i class="ri-checkbox-line"></i>
                        </label>
                    </div>
                </th>
                <th class="sortable" data-sort="titulo">
                    Elección <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="tipo">
                    Tipo <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="fecha_inicio">
                    Fecha Inicio <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="fecha_fin">
                    Fecha Fin <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="estado">
                    Estado <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="votos_count">
                    Votos <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="text-center" style="min-width: 140px; width: 140px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($elecciones as $eleccion)
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input eleccion-checkbox" type="checkbox" 
                               value="{{ $eleccion->id }}" 
                               id="eleccion_{{ $eleccion->id }}"
                               onchange="updateSelectAllState()">
                        <label class="form-check-label" for="eleccion_{{ $eleccion->id }}">
                            <i class="ri-checkbox-line"></i>
                        </label>
                    </div>
                </td>
                
                <!-- Columna de Elección -->
                <td>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                <i class="ri-vote-line text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $eleccion->titulo }}</h6>
                            <small class="text-muted">{{ $eleccion->organizacion->nombre ?? 'N/A' }}</small>
                            @if($eleccion->descripcion)
                                <br><small class="text-muted">{{ Str::limit($eleccion->descripcion, 50) }}</small>
                            @endif
                        </div>
                    </div>
                </td>
                
                <!-- Columna de Tipo -->
                <td>
                    <span class="badge bg-soft-secondary text-secondary">
                        {{ ucfirst($eleccion->tipo) }}
                    </span>
                </td>
                
                <!-- Columna de Fecha Inicio -->
                <td>
                    <div class="d-flex flex-column">
                        <span class="fw-semibold">{{ $eleccion->fecha_inicio->format('d/m/Y') }}</span>
                        <small class="text-muted">{{ $eleccion->fecha_inicio->format('H:i') }}</small>
                    </div>
                </td>
                
                <!-- Columna de Fecha Fin -->
                <td>
                    <div class="d-flex flex-column">
                        <span class="fw-semibold">{{ $eleccion->fecha_fin->format('d/m/Y') }}</span>
                        <small class="text-muted">{{ $eleccion->fecha_fin->format('H:i') }}</small>
                    </div>
                </td>
                
                <!-- Columna de Estado -->
                <td>
                    <div class="estado-eleccion" data-eleccion-id="{{ $eleccion->id }}" 
                         data-fecha-inicio="{{ $eleccion->fecha_inicio->format('Y-m-d H:i:s') }}" 
                         data-fecha-fin="{{ $eleccion->fecha_fin->format('Y-m-d H:i:s') }}"
                         data-estado-actual="{{ $eleccion->estado }}">
                        <span class="eleccion-badge {{ $eleccion->estado }}" id="badge-estado-{{ $eleccion->id }}">
                            {{ ucfirst($eleccion->estado) }}
                        </span>
                        <div class="countdown-timer" id="countdown-{{ $eleccion->id }}" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="ri-time-line me-2"></i>
                                    <span class="fw-medium" id="timer-text-{{ $eleccion->id }}">Calculando...</span>
                                </div>
                                <div class="countdown-indicator">
                                    <div class="countdown-dot"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- Columna de Votos -->
                <td>
                    <div class="d-flex flex-column">
                        <strong>{{ number_format($eleccion->votos->count()) }}</strong>
                        <div class="progress progress-custom mt-1">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </td>
                
                <!-- Columna de Acciones -->
                <td class="text-center" style="min-width: 140px; width: 140px;">
                    <div class="d-flex flex-column gap-1 align-items-center justify-content-center" style="width: 100%; max-width: 100px; margin: 0 auto; padding: 0 0.5rem;">
                        <!-- Primera fila de iconos -->
                        <div class="d-flex align-items-center gap-1">
                            <!-- Ver Candidatos -->
                            <a href="{{ route('elecciones.candidatos') }}" 
                               class="btn btn-soft-primary btn-sm" 
                               title="Ver Candidatos"
                               data-bs-toggle="tooltip">
                                <i class="ri-user-line"></i>
                            </a>
                            
                            <!-- Ver Resultados -->
                            <a href="{{ route('elecciones.resultados', $eleccion->id) }}" 
                               class="btn btn-soft-info btn-sm" 
                               title="Ver Resultados"
                               data-bs-toggle="tooltip">
                                <i class="ri-bar-chart-line"></i>
                            </a>
                            
                            <!-- Generar Links de Votación -->
                            <button type="button" 
                                    class="btn btn-soft-success btn-sm" 
                                    title="Generar Links de Votación"
                                    data-bs-toggle="tooltip"
                                    onclick="generarLinks({{ $eleccion->id }}, '{{ $eleccion->titulo }}')">
                                <i class="ri-links-line"></i>
                            </button>
                        </div>
                        
                        <!-- Segunda fila de iconos -->
                        <div class="d-flex align-items-center gap-1">
                            <!-- Editar -->
                            <a href="{{ route('elecciones.edit', $eleccion->id) }}" 
                               class="btn btn-soft-warning btn-sm" 
                               title="Editar"
                               data-bs-toggle="tooltip">
                                <i class="ri-edit-line"></i>
                            </a>
                            
                            <!-- Eliminar -->
                            <button type="button" 
                                    class="btn btn-soft-danger btn-sm" 
                                    title="Eliminar"
                                    data-bs-toggle="tooltip"
                                    onclick="eliminarEleccion({{ $eleccion->id }})">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="empty-state">
                        <i class="ri-vote-line text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No hay elecciones registradas</h5>
                        <p class="text-muted">Comienza creando tu primera elección</p>
                        <a href="{{ route('elecciones.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i>
                            Nueva Elección
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
