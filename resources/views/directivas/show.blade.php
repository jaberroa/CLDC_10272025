@extends('partials.layouts.master')

@section('title', 'Detalles de Directiva | CLDCI')
@section('title-sub', 'Gestión de Directiva')
@section('pagetitle', 'Detalles de Directiva')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/gridjs/theme/mermaid.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/miembros-index-header.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <!-- Header con información principal -->
        <div class="card shadow-sm mb-4">
            <div class="card-header miembros-index-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('directivas.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-group-line"></i>
                            Detalles de Directiva
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Información completa de la directiva de {{ $directiva->miembro->nombre_completo }}
                        </p>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('miembros.profile', $directiva->miembro) }}" class="btn btn-outline-light btn-sm">
                            <i class="ri-user-line me-1"></i> Ver Miembro
                        </a>
                        <a href="{{ route('directivas.edit', $directiva) }}" class="btn btn-outline-light btn-sm">
                            <i class="ri-edit-line me-1"></i> Editar
                        </a>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="deleteDirectiva({{ $directiva->id }}, '{{ $directiva->miembro->nombre_completo }}')">
                            <i class="ri-delete-bin-line me-1"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Información del Miembro -->
            <div class="col-xxl-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-user-line me-2"></i>Información del Miembro
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-lg bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-3">
                                @if($directiva->miembro->foto_url)
                                    <img src="{{ Storage::url($directiva->miembro->foto_url) }}" 
                                         class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <span class="text-primary fw-bold fs-2">{{ $directiva->miembro->iniciales }}</span>
                                @endif
                            </div>
                            <div>
                                <h5 class="mb-0 fw-semibold">{{ $directiva->miembro->nombre_completo }}</h5>
                                <p class="text-muted mb-0">{{ $directiva->miembro->cedula }}</p>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Email</label>
                                <div class="text-muted">{{ $directiva->miembro->email }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <div class="text-muted">{{ $directiva->miembro->telefono ?? 'No especificado' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Organización</label>
                                <div class="text-muted">{{ $directiva->miembro->organizacion->nombre ?? 'No especificada' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Estado de Membresía</label>
                                <div>
                                    <span class="badge bg-{{ $directiva->miembro->estado_membresia_color }}-subtle text-{{ $directiva->miembro->estado_membresia_color }}">
                                        {{ $directiva->miembro->estado_membresia_nombre }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Cargo -->
            <div class="col-xxl-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-briefcase-line me-2"></i>Información del Cargo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Órgano</label>
                                <div>
                                    <span class="badge bg-info-subtle text-info fs-6">
                                        {{ $directiva->organo->nombre }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Cargo</label>
                                <div>
                                    <span class="badge bg-primary-subtle text-primary fs-6">
                                        {{ $directiva->cargo->nombre }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Período de Directiva</label>
                                <div class="text-muted">
                                    {{ $directiva->periodo_directiva ?: 'Sin período asignado' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Estado</label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-{{ $directiva->estado_color }}-subtle rounded-circle d-flex align-items-center justify-content-center me-2">
                                        <i class="ri-{{ $directiva->estado === 'activo' ? 'check' : ($directiva->estado === 'inactivo' ? 'close' : 'pause') }}-line text-{{ $directiva->estado_color }}"></i>
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ $directiva->estado_color }}-subtle text-{{ $directiva->estado_color }}">
                                            {{ $directiva->estado_nombre }}
                                        </span>
                                        @if($directiva->es_vigente)
                                            <div class="text-success small">Vigente</div>
                                        @elseif($directiva->es_vencido)
                                            <div class="text-danger small">Vencido</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de Inicio</label>
                                <div class="text-muted">{{ $directiva->fecha_inicio->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de Fin</label>
                                <div class="text-muted">
                                    {{ $directiva->fecha_fin ? $directiva->fecha_fin->format('d/m/Y') : 'Sin fecha de fin' }}
                                </div>
                            </div>
                            @if($directiva->duracion_formateada)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Duración del Mandato</label>
                                <div class="text-muted">{{ $directiva->duracion_formateada }}</div>
                            </div>
                            @endif
                            <div class="col-12">
                                <label class="form-label fw-semibold">Observaciones</label>
                                <div class="text-muted">
                                    {{ $directiva->observaciones ?: 'Sin observaciones' }}
                                </div>
                            </div>
                        </div>

                        <!-- Información del Sistema -->
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="fw-semibold text-muted mb-3">
                                    <i class="ri-information-line me-2"></i>Información del Sistema
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fecha de Creación</label>
                                <div class="text-muted">{{ $directiva->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($directiva->updated_at != $directiva->created_at)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Última Actualización</label>
                                <div class="text-muted">{{ $directiva->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @endif
                            @if($directiva->creadoPor)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Creado por</label>
                                <div class="text-muted">{{ $directiva->creadoPor->name }}</div>
                            </div>
                            @endif
                            @if($directiva->actualizadoPor)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Actualizado por</label>
                                <div class="text-muted">{{ $directiva->actualizadoPor->name }}</div>
                            </div>
                            @endif
                        </div>

                        <!-- Acciones -->
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="fw-semibold text-muted mb-3">
                                    <i class="ri-settings-3-line me-2"></i>Acciones
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('miembros.profile', $directiva->miembro) }}" class="btn btn-outline-primary w-100">
                                    <i class="ri-user-line me-1"></i> Ver Miembro
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('directivas.edit', $directiva) }}" class="btn btn-outline-warning w-100">
                                    <i class="ri-edit-line me-1"></i> Editar Directiva
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('directivas.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="ri-arrow-left-line me-1"></i> Volver a Lista
                                </a>
                            </div>
                            
                            @if($directiva->estado === 'activo')
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="deactivateDirectiva({{ $directiva->id }})">
                                    <i class="ri-pause-line me-1"></i> Desactivar
                                </button>
                            </div>
                            @else
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-success w-100" onclick="activateDirectiva({{ $directiva->id }})">
                                    <i class="ri-play-line me-1"></i> Activar
                                </button>
                            </div>
                            @endif
                            
                            @if($directiva->estado !== 'suspendido')
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-warning w-100" onclick="suspendDirectiva({{ $directiva->id }})">
                                    <i class="ri-alarm-warning-line me-1"></i> Suspender
                                </button>
                            </div>
                            @endif
                            
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-danger w-100" onclick="deleteDirectiva({{ $directiva->id }}, '{{ $directiva->miembro->nombre_completo }}')">
                                    <i class="ri-delete-bin-line me-1"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
// Función global para eliminar directiva
function deleteDirectiva(directivaId, directivaName) {
    showDeleteConfirmation({
        title: directivaName,
        type: 'directiva',
        onConfirm: () => {
            // Mostrar toast de carga
            showInfoToast('Eliminando directiva...', 'Procesando');
            
            // Realizar eliminación por AJAX
            fetch(`{{ url('directivas') }}/${directivaId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    // Mostrar toast de éxito
                    showSuccessToast(`Directiva "${directivaName}" eliminada exitosamente`);
                    
                    // Redirigir a la lista después de un breve delay
                    setTimeout(() => {
                        window.location.href = '{{ route("directivas.index") }}';
                    }, 2000);
                } else {
                    throw new Error('Error al eliminar la directiva');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorToast('Error al eliminar la directiva');
            });
        }
    });
}

function activateDirectiva(directivaId) {
    showInfoToast('Activando directiva...', 'Procesando');
    
    fetch(`{{ url('directivas') }}/${directivaId}/activate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast(data.message);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error al activar la directiva');
    });
}

function deactivateDirectiva(directivaId) {
    showInfoToast('Desactivando directiva...', 'Procesando');
    
    fetch(`{{ url('directivas') }}/${directivaId}/deactivate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast(data.message);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error al desactivar la directiva');
    });
}

function suspendDirectiva(directivaId) {
    showInfoToast('Suspendiendo directiva...', 'Procesando');
    
    fetch(`{{ url('directivas') }}/${directivaId}/suspend`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast(data.message);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Error al suspender la directiva');
    });
}
</script>
@endsection