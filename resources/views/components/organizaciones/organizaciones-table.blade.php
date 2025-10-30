@props(['organizaciones'])

<div class="table-responsive">
    <table class="table table-hover text-nowrap organizaciones-table">
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
                <th class="sortable" data-sort="nombre">
                    Organización <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="tipo">
                    Tipo <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="estado">
                    Estado <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="direccion">
                    Dirección <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="created_at">
                    Fecha Registro <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="text-center" style="min-width: 140px; width: 140px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($organizaciones as $organizacion)
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input organizacion-checkbox" type="checkbox" 
                               value="{{ $organizacion->id }}" 
                               id="organizacion_{{ $organizacion->id }}"
                               onchange="updateSelectAllState()">
                        <label class="form-check-label" for="organizacion_{{ $organizacion->id }}">
                            <i class="ri-checkbox-line"></i>
                        </label>
                    </div>
                </td>
                
                <!-- Columna de Organización -->
                <td>
                    <x-organizaciones.organizacion-avatar :organizacion="$organizacion" />
                </td>
                
                <!-- Columna de Tipo -->
                <td>
                    <x-organizaciones.tipo-badge :tipo="$organizacion->tipo" />
                </td>
                
                <!-- Columna de Estado -->
                <td>
                    <x-organizaciones.estado-badge :estado="$organizacion->estado" />
                </td>
                
                <!-- Columna de Dirección -->
                <td>
                    <span class="fw-semibold">{{ Str::limit($organizacion->direccion, 25) }}</span>
                </td>
                
                <!-- Columna de Fecha Registro -->
                <td>
                    <span class="fw-semibold">{{ $organizacion->created_at->format('d/m/Y') }}</span>
                </td>
                
                <!-- Columna de Acciones -->
                <td class="text-center" style="min-width: 140px; width: 140px;">
                    <div class="d-flex flex-column gap-1 align-items-center justify-content-center" style="width: 100%; max-width: 100px; margin: 0 auto; padding: 0 0.5rem;">
                        <!-- Primera fila de iconos -->
                        <div class="d-flex align-items-center gap-1">
                            <!-- Ver Perfil -->
                            <a href="{{ route('organizaciones.profile', $organizacion->id) }}" 
                               class="btn btn-soft-primary btn-sm" 
                               title="Ver Perfil"
                               data-bs-toggle="tooltip">
                                <i class="ri-building-line"></i>
                            </a>
                            
                            <!-- Ver Miembros -->
                            <a href="{{ route('organizaciones.miembros', $organizacion->id) }}" 
                               class="btn btn-soft-info btn-sm" 
                               title="Ver Miembros"
                               data-bs-toggle="tooltip">
                                <i class="ri-group-line"></i>
                            </a>
                            
                            <!-- Configuración -->
                            <button type="button" 
                                    class="btn btn-soft-success btn-sm" 
                                    title="Configuración"
                                    data-bs-toggle="tooltip"
                                    onclick="openConfigModal({{ $organizacion->id }}, '{{ $organizacion->nombre }}')">
                                <i class="ri-settings-line"></i>
                            </button>
                        </div>
                        
                        <!-- Segunda fila de iconos -->
                        <div class="d-flex align-items-center gap-1">
                            <!-- Editar -->
                            <a href="{{ route('organizaciones.edit', $organizacion->id) }}" 
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
                                    onclick="deleteOrganizacion({{ $organizacion->id }}, '{{ $organizacion->nombre }}')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="ri-building-line fs-1 mb-3 d-block"></i>
                            <h5>No hay organizaciones registradas</h5>
                            <p>Comience agregando su primera organización.</p>
                            <a href="{{ route('organizaciones.create') }}" class="btn btn-primary">
                                <i class="ri-building-add-line me-1"></i> Agregar Primera Organización
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

