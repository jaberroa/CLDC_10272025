@props(['miembro'])

<td>
    <div class="d-flex flex-column gap-1">
        <!-- Primera fila de iconos -->
        <div class="d-flex align-items-center gap-1">
            <!-- Ver Perfil -->
            <a href="{{ route('miembros.profile', $miembro->id) }}" 
               class="btn btn-soft-primary btn-sm" 
               title="Ver Perfil"
               data-bs-toggle="tooltip">
                <i class="ri-user-line fs-4"></i>
            </a>
            
            <!-- Carnet Digital -->
            <a href="{{ route('carnet.selector', $miembro->id) }}" 
               class="btn btn-soft-info btn-sm" 
               title="Carnet Digital"
               data-bs-toggle="tooltip">
                <i class="ri-id-card-line fs-4"></i>
            </a>
            
            <!-- Subir Documento -->
            <button type="button" 
                    class="btn btn-soft-success btn-sm" 
                    title="Subir Documento"
                    data-bs-toggle="tooltip"
                    onclick="openDocumentUpload({{ $miembro->id }}, '{{ $miembro->nombre_completo }}')">
                <i class="ri-upload-line fs-4"></i>
            </button>
        </div>
        
        <!-- Segunda fila de iconos -->
        <div class="d-flex align-items-center gap-1">
            <!-- Editar -->
            <a href="{{ route('miembros.edit', $miembro->id) }}" 
               class="btn btn-soft-warning btn-sm" 
               title="Editar"
               data-bs-toggle="tooltip">
                <i class="ri-edit-line fs-4"></i>
            </a>
            
            <!-- Eliminar -->
            <button type="button" 
                    class="btn btn-soft-danger btn-sm" 
                    title="Eliminar"
                    data-bs-toggle="tooltip"
                    onclick="deleteMember({{ $miembro->id }}, '{{ $miembro->nombre_completo }}')">
                <i class="ri-delete-bin-line fs-4"></i>
            </button>
        </div>
    </div>
</td>
