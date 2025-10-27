@extends('partials.layouts.master')

@section('title', 'Editar Elección | CLDCI')
@section('title-sub', 'Editar Elección')
@section('pagetitle', 'Editar Elección')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/elecciones-edit-header.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header elecciones-edit-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('elecciones.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-vote-line"></i>
                            Editar Elección: {{ $eleccion->titulo }}
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Modifique la información de la elección según sea necesario
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>
                            <i class="ri-error-warning-line me-2"></i>
                            Error al actualizar:
                        </strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-check-line me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('elecciones.update', $eleccion->id) }}" method="POST" id="formEditarEleccion">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">
                                    Título de la Elección <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('titulo') is-invalid @enderror" 
                                       id="titulo" 
                                       name="titulo" 
                                       value="{{ old('titulo', $eleccion->titulo) }}" 
                                       required 
                                       placeholder="Ej: Elección Junta Directiva 2025">
                                @error('titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">
                                    Tipo de Elección <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tipo') is-invalid @enderror" 
                                        id="tipo" 
                                        name="tipo" 
                                        required>
                                    <option value="">Seleccione...</option>
                                    <option value="directiva" {{ old('tipo', $eleccion->tipo) == 'directiva' ? 'selected' : '' }}>
                                        Junta Directiva
                                    </option>
                                    <option value="comision" {{ old('tipo', $eleccion->tipo) == 'comision' ? 'selected' : '' }}>
                                        Comisión
                                    </option>
                                    <option value="especial" {{ old('tipo', $eleccion->tipo) == 'especial' ? 'selected' : '' }}>
                                        Especial
                                    </option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="3" 
                                  placeholder="Describe el propósito de esta elección...">{{ old('descripcion', $eleccion->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="organizacion_id" class="form-label">
                                    Organización <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('organizacion_id') is-invalid @enderror" 
                                        id="organizacion_id" 
                                        name="organizacion_id" 
                                        required>
                                    <option value="">Seleccione una organización...</option>
                                    @foreach($organizaciones as $org)
                                        <option value="{{ $org->id }}" {{ old('organizacion_id', $eleccion->organizacion_id) == $org->id ? 'selected' : '' }}>
                                            {{ $org->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('organizacion_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio_votacion" class="form-label">
                                    Fecha de Inicio de Votación <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('fecha_inicio_votacion') is-invalid @enderror" 
                                       id="fecha_inicio_votacion" 
                                       name="fecha_inicio_votacion" 
                                       value="{{ old('fecha_inicio_votacion', $eleccion->fecha_inicio->format('Y-m-d')) }}" 
                                       required>
                                @error('fecha_inicio_votacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Votación -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="hora_inicio" class="form-label">
                                    Hora de Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="time" 
                                       class="form-control @error('hora_inicio') is-invalid @enderror" 
                                       id="hora_inicio" 
                                       name="hora_inicio" 
                                       value="{{ old('hora_inicio', $eleccion->fecha_inicio->format('H:i')) }}" 
                                       required>
                                @error('hora_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="duracion_minutos" class="form-label">
                                    Duración (minutos) <span class="text-danger">*</span>
                                </label>
                                @php
                                    $duracionActual = $eleccion->fecha_inicio->diffInMinutes($eleccion->fecha_fin);
                                @endphp
                                <input type="number" 
                                       class="form-control @error('duracion_minutos') is-invalid @enderror" 
                                       id="duracion_minutos" 
                                       name="duracion_minutos" 
                                       value="{{ old('duracion_minutos', $duracionActual) }}" 
                                       min="15"
                                       step="15"
                                       required>
                                <small class="text-muted">Mínimo 15 minutos, múltiplos de 15</small>
                                @error('duracion_minutos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label d-block">Estado Actual</label>
                                <div class="form-check form-switch" style="padding-top: 8px;">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           role="switch" 
                                           id="estado_activo" 
                                           name="estado_activo" 
                                           value="1"
                                           {{ old('estado_activo', $eleccion->estaActiva() ? '1' : '0') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado_activo">
                                        <span id="estadoLabel">{{ $eleccion->estaActiva() ? 'Votación Activa' : 'Votación Inactiva' }}</span>
                                    </label>
                                </div>
                                <small class="text-muted">Cambia el estado de la votación</small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="hora_fin_calculada" class="form-label">
                                    Hora de Fin (calculada)
                                </label>
                                <input type="text" 
                                       class="form-control bg-light" 
                                       id="hora_fin_calculada" 
                                       readonly 
                                       value="{{ $eleccion->fecha_fin->format('H:i') }}">
                                <small class="text-muted">Se calcula automáticamente</small>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Candidatos -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary-subtle">
                                    <h5 class="mb-0">
                                        <i class="ri-user-star-line me-2"></i>
                                        Candidatos
                                    </h5>
                                    <small class="text-muted">Gestiona los candidatos que participarán en esta elección</small>
                                </div>
                                <div class="card-body">
                                    <div id="candidatos-container">
                                        <!-- Los candidatos se agregan aquí dinámicamente -->
                                    </div>
                                    
                                    <button type="button" class="btn btn-outline-primary" onclick="agregarCandidato()">
                                        <i class="ri-add-line me-1"></i>
                                        Agregar Candidato
                                    </button>
                                    
                                    <div class="alert alert-info mt-3" id="alertNoCandidatos">
                                        <i class="ri-information-line me-2"></i>
                                        Aún no has agregado candidatos. Haz clic en "Agregar Candidato" para comenzar.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('elecciones.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>
                            Actualizar Elección
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Calcular hora de fin automáticamente
function calcularHoraFin() {
    const horaInicio = document.getElementById('hora_inicio').value;
    const duracion = parseInt(document.getElementById('duracion_minutos').value) || 0;
    
    if (horaInicio && duracion > 0) {
        const [horas, minutos] = horaInicio.split(':').map(Number);
        const fecha = new Date();
        fecha.setHours(horas, minutos, 0);
        fecha.setMinutes(fecha.getMinutes() + duracion);
        
        const horaFin = fecha.getHours().toString().padStart(2, '0') + ':' + 
                        fecha.getMinutes().toString().padStart(2, '0');
        document.getElementById('hora_fin_calculada').value = horaFin;
    }
}

// Toggle estado label
document.getElementById('estado_activo').addEventListener('change', function() {
    const label = document.getElementById('estadoLabel');
    if (this.checked) {
        label.textContent = 'Votación Activa';
        label.classList.remove('text-muted');
        label.classList.add('text-success');
    } else {
        label.textContent = 'Votación Inactiva';
        label.classList.remove('text-success');
        label.classList.add('text-muted');
    }
});

// Listeners para cálculo automático
document.getElementById('hora_inicio').addEventListener('change', calcularHoraFin);
document.getElementById('duracion_minutos').addEventListener('input', calcularHoraFin);

// Calcular al cargar
document.addEventListener('DOMContentLoaded', calcularHoraFin);

// ========================================
// GESTIÓN DE CANDIDATOS
// ========================================

let candidatosCount = 0;
const candidatos = [];

// Datos de cargos y miembros desde el servidor
const cargosData = @json($cargos);
const miembrosData = @json($miembros);

// Candidatos existentes de la elección
const candidatosExistentes = @json($eleccion->candidatos);

function agregarCandidato(candidatoExistente = null) {
    candidatosCount++;
    const id = 'candidato_' + candidatosCount;
    
    // Si hay candidato existente, usar sus datos
    const miembroId = candidatoExistente?.miembro_id || '';
    const cargoId = candidatoExistente?.cargo_id || '';
    const propuestas = candidatoExistente?.propuestas || '';
    const orden = candidatoExistente?.orden || candidatosCount;
    
    // Obtener el nombre del miembro si existe
    let nombre = '';
    if (candidatoExistente?.miembro_id) {
        const miembro = miembrosData.find(m => m.id == candidatoExistente.miembro_id);
        nombre = miembro ? miembro.nombre_completo : '';
    }
    
    const html = `
        <div class="card mb-3 candidato-card" id="${id}" data-index="${candidatosCount}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="mb-0">
                        <i class="ri-user-star-line me-2"></i>
                        Candidato #${candidatosCount}
                    </h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarCandidato('${id}')">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Miembro <span class="text-danger">*</span></label>
                            <select class="form-select" name="candidatos[${candidatosCount}][miembro_id]" required onchange="actualizarNombreCandidato(this, ${candidatosCount})">
                                <option value="">Seleccione un miembro...</option>
                                ${miembrosData.map(m => `<option value="${m.id}" data-nombre="${m.nombre_completo}" ${m.id == miembroId ? 'selected' : ''}>${m.nombre_completo}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Cargo al que Aspira <span class="text-danger">*</span></label>
                            <select class="form-select" name="candidatos[${candidatosCount}][cargo_id]" required>
                                <option value="">Seleccione un cargo...</option>
                                ${cargosData.map(c => `<option value="${c.id}" data-nombre="${c.nombre}" ${c.id == cargoId ? 'selected' : ''}>${c.nombre}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Propuestas (opcional)</label>
                            <textarea class="form-control" name="candidatos[${candidatosCount}][propuestas]" rows="2" placeholder="Describe brevemente las propuestas del candidato...">${propuestas}</textarea>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="candidatos[${candidatosCount}][nombre]" id="nombre_${candidatosCount}" value="${nombre}">
                <input type="hidden" name="candidatos[${candidatosCount}][cargo_nombre]" id="cargo_nombre_${candidatosCount}">
                <input type="hidden" name="candidatos[${candidatosCount}][orden]" value="${orden}">
            </div>
        </div>
    `;
    
    document.getElementById('candidatos-container').insertAdjacentHTML('beforeend', html);
    
    // Si hay nombre pre-cargado, actualizarlo
    if (nombre && miembroId) {
        document.getElementById('nombre_' + candidatosCount).value = nombre;
    }
    
    // Si es un candidato existente con miembro seleccionado, actualizar el nombre
    if (candidatoExistente && candidatoExistente.miembro_id) {
        const selectElement = document.querySelector(`select[name="candidatos[${candidatosCount}][miembro_id]"]`);
        if (selectElement) {
            actualizarNombreCandidato(selectElement, candidatosCount);
        }
    }
    
    actualizarEstadoCandidatos();
}

function eliminarCandidato(id) {
    Swal.fire({
        title: '¿Eliminar candidato?',
        text: '¿Estás seguro de eliminar este candidato?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(id).remove();
            actualizarEstadoCandidatos();
            Swal.fire('Eliminado', 'El candidato ha sido eliminado.', 'success');
        }
    });
}

function actualizarNombreCandidato(select, index) {
    const selectedOption = select.options[select.selectedIndex];
    const nombre = selectedOption.getAttribute('data-nombre');
    document.getElementById('nombre_' + index).value = nombre || '';
}

function actualizarEstadoCandidatos() {
    const totalCandidatos = document.querySelectorAll('.candidato-card').length;
    const alert = document.getElementById('alertNoCandidatos');
    
    if (totalCandidatos === 0) {
        alert.style.display = 'block';
    } else {
        alert.style.display = 'none';
    }
}

// Cargar candidatos existentes al iniciar
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔵 Iniciando carga de vista editar elección');
    
    // Cargar candidatos existentes
    if (candidatosExistentes && candidatosExistentes.length > 0) {
        console.log('📋 Cargando candidatos existentes:', candidatosExistentes.length);
        candidatosExistentes.forEach(candidato => {
            agregarCandidato(candidato);
        });
    } else {
        console.log('⚠️ No hay candidatos existentes');
    }
    
    actualizarEstadoCandidatos();
    
    // Validar que haya al menos 2 candidatos antes de enviar
    const form = document.getElementById('formEditarEleccion');
    if (form) {
        console.log('✅ Formulario encontrado, agregando listener');
        
        form.addEventListener('submit', function(e) {
            console.log('📤 Submit event triggered');
            const totalCandidatos = document.querySelectorAll('.candidato-card').length;
            console.log('👥 Total candidatos:', totalCandidatos);
            
            if (totalCandidatos < 2) {
                e.preventDefault();
                console.log('❌ Validación falló: menos de 2 candidatos');
                Swal.fire({
                    icon: 'warning',
                    title: 'Candidatos Insuficientes',
                    text: 'Debes agregar al menos 2 candidatos para actualizar la elección.',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            
            // Log de datos del formulario antes de enviar
            const formData = new FormData(form);
            console.log('📦 Datos del formulario:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}:`, value);
            }
            
            // Si pasa la validación, mostrar indicador de carga
            console.log('✅ Validación pasada, enviando formulario');
            
            // Mostrar indicador de carga pero permitir el envío del formulario
            Swal.fire({
                title: 'Actualizando...',
                text: 'Por favor espera mientras se actualiza la elección.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Permitir que el formulario se envíe normalmente
            // No hacer preventDefault() aquí
        });
    } else {
        console.error('❌ Formulario no encontrado');
    }
});
</script>
@endsection
