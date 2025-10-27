@props(['miembros'])

<div class="table-responsive">
    <table class="table table-hover text-nowrap miembros-table">
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
                <th class="sortable" data-sort="nombre_completo">
                    Miembro <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="tipo_membresia">
                    Tipo <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="estado_membresia">
                    Estado <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="organizacion_id">
                    Organización <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="sortable" data-sort="fecha_ingreso">
                    Fecha Ingreso <i class="ri-arrow-up-down-line ms-1"></i>
                </th>
                <th class="text-center" style="min-width: 140px; width: 140px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($miembros as $miembro)
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input member-checkbox" type="checkbox" 
                               value="{{ $miembro->id }}" 
                               id="member_{{ $miembro->id }}"
                               onchange="updateSelectAllState()">
                        <label class="form-check-label" for="member_{{ $miembro->id }}">
                            <i class="ri-checkbox-line"></i>
                        </label>
                    </div>
                </td>
                
                <!-- Columna de Miembro -->
                <td>
                    <x-miembros.miembro-avatar :miembro="$miembro" />
                </td>
                
                <!-- Columna de Tipo de Membresía -->
                <td>
                    <x-miembros.tipo-membresia :tipo="$miembro->tipo_membresia" />
                </td>
                
                <!-- Columna de Estado -->
                <td>
                    <x-miembros.estado-badge :estado="$miembro->estadoMembresia" />
                </td>
                
                <!-- Columna de Organización -->
                <td>
                    <span class="fw-semibold">{{ Str::limit($miembro->organizacion->nombre, 20) }}</span>
                </td>
                
                <!-- Columna de Fecha Ingreso -->
                <td>
                    <span class="fw-semibold">{{ $miembro->fecha_ingreso->format('d/m/Y') }}</span>
                </td>
                
                <!-- Columna de Acciones -->
                <td class="text-center" style="min-width: 140px; width: 140px;">
                    <div class="d-flex flex-column gap-1 align-items-center justify-content-center" style="width: 100%; max-width: 100px; margin: 0 auto; padding: 0 0.5rem;">
                        <!-- Primera fila de iconos -->
                        <div class="d-flex align-items-center gap-1">
                            <!-- Ver Perfil -->
                            <a href="{{ route('miembros.profile', $miembro->id) }}" 
                               class="btn btn-soft-primary btn-sm" 
                               title="Ver Perfil"
                               data-bs-toggle="tooltip">
                                <i class="ri-user-line"></i>
                            </a>
                            
                            <!-- Carnet Digital -->
                            <a href="{{ route('carnet.selector', $miembro->id) }}" 
                               class="btn btn-soft-info btn-sm" 
                               title="Carnet Digital"
                               data-bs-toggle="tooltip">
                                <i class="ri-id-card-line"></i>
                            </a>
                            
                            <!-- Subir Documento -->
                            <button type="button" 
                                    class="btn btn-soft-success btn-sm" 
                                    title="Subir Documento"
                                    data-bs-toggle="tooltip"
                                    onclick="openDocumentUpload({{ $miembro->id }}, '{{ $miembro->nombre_completo }}')">
                                <i class="ri-upload-line"></i>
                            </button>
                        </div>
                        
                        <!-- Segunda fila de iconos -->
                        <div class="d-flex align-items-center gap-1">
                            <!-- Editar -->
                            <a href="{{ route('miembros.edit', $miembro->id) }}" 
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
                                    onclick="deleteMember({{ $miembro->id }}, '{{ $miembro->nombre_completo }}')">
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
                            <i class="ri-group-line fs-1 mb-3 d-block"></i>
                            <h5>No hay miembros registrados</h5>
                            <p>Comience agregando su primer miembro.</p>
                            <a href="{{ route('miembros.create') }}" class="btn btn-primary">
                                <i class="ri-user-add-line me-1"></i> Agregar Primer Miembro
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
