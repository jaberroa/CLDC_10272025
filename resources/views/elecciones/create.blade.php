@extends('partials.layouts.master')

@section('title', 'Nueva Elección | CLDCI')
@section('title-sub', 'Crear Nueva Elección')
@section('pagetitle', 'Nueva Elección')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/elecciones-create-header.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header elecciones-create-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('elecciones.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-add-line"></i>
                            Crear Nueva Elección
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Complete la información de la elección para registrarla en el sistema
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('elecciones.store') }}" method="POST">
                    @csrf
                    
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
                                       value="{{ old('titulo') }}" 
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
                                <div class="input-group">
                                    <select class="form-select @error('tipo') is-invalid @enderror" 
                                            id="tipo" 
                                            name="tipo" 
                                            required>
                                        <option value="">Seleccione...</option>
                                        @foreach($tiposElecciones as $tipoEleccion)
                                            <option value="{{ $tipoEleccion->slug }}" 
                                                    data-icono="{{ $tipoEleccion->icono }}"
                                                    data-color="{{ $tipoEleccion->color }}"
                                                    {{ old('tipo') == $tipoEleccion->slug ? 'selected' : '' }}>
                                                {{ $tipoEleccion->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" 
                                            class="btn btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalNuevoTipo"
                                            title="Crear nuevo tipo">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                                @error('tipo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Haz clic en + para crear un nuevo tipo</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="3" 
                                  placeholder="Describe el propósito de esta elección...">{{ old('descripcion') }}</textarea>
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
                                        <option value="{{ $org->id }}" {{ old('organizacion_id') == $org->id ? 'selected' : '' }}>
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
                                       value="{{ old('fecha_inicio_votacion', date('Y-m-d')) }}" 
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
                                       value="{{ old('hora_inicio', '09:00') }}" 
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
                                <input type="number" 
                                       class="form-control @error('duracion_minutos') is-invalid @enderror" 
                                       id="duracion_minutos" 
                                       name="duracion_minutos" 
                                       value="{{ old('duracion_minutos', 120) }}" 
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
                                <label class="form-label d-block">Estado Inicial</label>
                                <div class="form-check form-switch" style="padding-top: 8px;">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           role="switch" 
                                           id="estado_activo" 
                                           name="estado_activo" 
                                           value="1"
                                           {{ old('estado_activo', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado_activo">
                                        <span id="estadoLabel">Votación Activa</span>
                                    </label>
                                </div>
                                <small class="text-muted">Si está activa, la votación iniciará a la hora programada</small>
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
                                       value="--:--">
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
                                    <small class="text-muted">Agrega los candidatos que participarán en esta elección</small>
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

                    {{-- Campos adicionales deshabilitados temporalmente hasta ejecutar migraciones
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="votos_por_persona" class="form-label">Votos por Persona</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="votos_por_persona" 
                                       name="votos_por_persona" 
                                       value="1" 
                                       min="1"
                                       disabled>
                                <small class="text-muted">Número de candidatos que puede elegir cada votante</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="requiere_quorum" 
                                           name="requiere_quorum" 
                                           disabled>
                                    <label class="form-check-label" for="requiere_quorum">
                                        Requiere Quórum
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="quorum_minimo" class="form-label">Quórum Mínimo (%)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="quorum_minimo" 
                                       name="quorum_minimo" 
                                       min="1" 
                                       max="100"
                                       placeholder="Ej: 50"
                                       disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="permite_abstencion" 
                                           name="permite_abstencion" 
                                           disabled>
                                    <label class="form-check-label" for="permite_abstencion">
                                        Permitir Abstención
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('elecciones.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>
                            Crear Elección
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info mt-3">
            <h5 class="alert-heading">
                <i class="ri-information-line me-2"></i>
                Próximos Pasos
            </h5>
            <p class="mb-2">Después de crear la elección, deberás:</p>
            <ol class="mb-0">
                <li>Registrar los candidatos participantes</li>
                <li>Definir el padrón electoral (quiénes pueden votar)</li>
                <li>Activar la elección cuando esté lista</li>
            </ol>
        </div>
    </div>
</div>

<!-- Modal: Nuevo Tipo de Elección -->
<div class="modal fade" id="modalNuevoTipo" tabindex="-1" aria-labelledby="modalNuevoTipoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title" id="modalNuevoTipoLabel">
                    <i class="ri-add-line me-2"></i>
                    Crear Nuevo Tipo de Elección
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevoTipo">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_nombre" class="form-label">
                            Nombre del Tipo <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="modal_nombre" 
                               required 
                               placeholder="Ej: Elección de Vocal">
                    </div>

                    <div class="mb-3">
                        <label for="modal_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" 
                                  id="modal_descripcion" 
                                  rows="2" 
                                  placeholder="Describe el propósito..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="modal_icono" class="form-label">Icono</label>
                                <select class="form-select" id="modal_icono">
                                    <option value="ri-checkbox-circle-line">📋 Checkbox</option>
                                    <option value="ri-team-line">👥 Team</option>
                                    <option value="ri-group-line">👨‍👩‍👧‍👦 Group</option>
                                    <option value="ri-star-line">⭐ Star</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="modal_color" class="form-label">Color</label>
                                <select class="form-select" id="modal_color">
                                    <option value="primary">🔵 Azul</option>
                                    <option value="success">🟢 Verde</option>
                                    <option value="warning">🟡 Amarillo</option>
                                    <option value="danger">🔴 Rojo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="modal_activo" checked>
                        <label class="form-check-label" for="modal_activo">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarTipo">
                        <i class="ri-save-line me-1"></i>
                        Crear Tipo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
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

function agregarCandidato() {
    candidatosCount++;
    const id = 'candidato_' + candidatosCount;
    
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
                                ${miembrosData.map(m => `<option value="${m.id}" data-nombre="${m.nombre_completo}">${m.nombre_completo}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Cargo al que Aspira <span class="text-danger">*</span></label>
                            <select class="form-select" name="candidatos[${candidatosCount}][cargo_id]" required>
                                <option value="">Seleccione un cargo...</option>
                                ${cargosData.map(c => `<option value="${c.id}" data-nombre="${c.nombre}">${c.nombre}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Propuestas (opcional)</label>
                            <textarea class="form-control" name="candidatos[${candidatosCount}][propuestas]" rows="2" placeholder="Describe brevemente las propuestas del candidato..."></textarea>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="candidatos[${candidatosCount}][nombre]" id="nombre_${candidatosCount}">
                <input type="hidden" name="candidatos[${candidatosCount}][cargo_nombre]" id="cargo_nombre_${candidatosCount}">
                <input type="hidden" name="candidatos[${candidatosCount}][orden]" value="${candidatosCount}">
            </div>
        </div>
    `;
    
    document.getElementById('candidatos-container').insertAdjacentHTML('beforeend', html);
    actualizarEstadoCandidatos();
}

function eliminarCandidato(id) {
    if (confirm('¿Estás seguro de eliminar este candidato?')) {
        document.getElementById(id).remove();
        actualizarEstadoCandidatos();
    }
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

// Validar que haya al menos 2 candidatos antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const totalCandidatos = document.querySelectorAll('.candidato-card').length;
    
    if (totalCandidatos < 2) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Candidatos Insuficientes',
            text: 'Debes agregar al menos 2 candidatos para crear una elección.',
            confirmButtonText: 'Entendido'
        });
        return false;
    }
});

// Agregar primer candidato automáticamente
document.addEventListener('DOMContentLoaded', function() {
    // No agregar automáticamente, dejar que el usuario lo haga
    actualizarEstadoCandidatos();
});
</script>
<script>
    // Validación de fechas
    document.getElementById('fecha_inicio').addEventListener('change', function() {
        const fechaInicio = new Date(this.value);
        const fechaFinInput = document.getElementById('fecha_fin');
        
        if (fechaFinInput.value) {
            const fechaFin = new Date(fechaFinInput.value);
            if (fechaFin <= fechaInicio) {
                showErrorToast('La fecha de fin debe ser posterior a la fecha de inicio');
                fechaFinInput.value = '';
            }
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Manejar creación de nuevo tipo desde el modal
document.getElementById('formNuevoTipo').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btnGuardar = document.getElementById('btnGuardarTipo');
    const btnText = btnGuardar.innerHTML;
    
    // Deshabilitar botón
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
    
    const formData = {
        nombre: document.getElementById('modal_nombre').value,
        descripcion: document.getElementById('modal_descripcion').value,
        icono: document.getElementById('modal_icono').value,
        color: document.getElementById('modal_color').value,
        activo: document.getElementById('modal_activo').checked ? 1 : 0,
        orden: 999
    };
    
    fetch('{{ route("tipos-elecciones.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Agregar nueva opción al select
            const select = document.getElementById('tipo');
            const option = document.createElement('option');
            option.value = data.tipo.slug;
            option.textContent = data.tipo.nombre;
            option.setAttribute('data-icono', data.tipo.icono);
            option.setAttribute('data-color', data.tipo.color);
            option.selected = true;
            select.appendChild(option);
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoTipo'));
            modal.hide();
            
            // Limpiar formulario
            document.getElementById('formNuevoTipo').reset();
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Creado!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al crear el tipo de elección'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al guardar el tipo de elección'
        });
    })
    .finally(() => {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = btnText;
    });
});
</script>
@endsection

