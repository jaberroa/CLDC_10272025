@extends('partials.layouts.master')

@section('title', 'Agregar Evento | CLDCI')
@section('title-sub', 'Gestión de Cronograma')
@section('pagetitle', 'Agregar Nuevo Evento')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/miembros-create-header.css') }}">
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card shadow-sm">
            <div class="card-header miembros-create-header">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <a href="{{ route('cronograma-directiva.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-calendar-event-line"></i>
                            Agregar Nuevo Evento
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Complete la información del evento para agregarlo al cronograma
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('cronograma-directiva.store') }}" method="POST" id="cronograma-form">
                    @csrf
                    
                    <!-- Información del Evento -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="ri-calendar-line me-2"></i>
                                Información del Evento
                            </h5>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label for="titulo" class="form-label">Título del Evento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="tipo_evento" class="form-label">Tipo de Evento <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_evento') is-invalid @enderror" 
                                    id="tipo_evento" name="tipo_evento" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="reunion" {{ old('tipo_evento') == 'reunion' ? 'selected' : '' }}>Reunión</option>
                                <option value="asamblea" {{ old('tipo_evento') == 'asamblea' ? 'selected' : '' }}>Asamblea</option>
                                <option value="capacitacion" {{ old('tipo_evento') == 'capacitacion' ? 'selected' : '' }}>Capacitación</option>
                                <option value="eleccion" {{ old('tipo_evento') == 'eleccion' ? 'selected' : '' }}>Elección</option>
                                <option value="conferencia" {{ old('tipo_evento') == 'conferencia' ? 'selected' : '' }}>Conferencia</option>
                            </select>
                            @error('tipo_evento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Descripción detallada del evento">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Fechas y Horarios -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="ri-time-line me-2"></i>
                                Fechas y Horarios
                            </h5>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                   id="fecha_inicio" name="fecha_inicio" 
                                   value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                   id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}">
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('estado') is-invalid @enderror" 
                                    id="estado" name="estado" required>
                                <option value="">Seleccionar estado</option>
                                <option value="programado" {{ old('estado', 'programado') == 'programado' ? 'selected' : '' }}>Programado</option>
                                <option value="en_curso" {{ old('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                                <option value="completado" {{ old('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                                <option value="cancelado" {{ old('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                            <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror" 
                                   id="hora_inicio" name="hora_inicio" value="{{ old('hora_inicio') }}">
                            @error('hora_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="hora_fin" class="form-label">Hora de Fin</label>
                            <input type="time" class="form-control @error('hora_fin') is-invalid @enderror" 
                                   id="hora_fin" name="hora_fin" value="{{ old('hora_fin') }}">
                            @error('hora_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Ubicación y Responsables -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="ri-map-pin-line me-2"></i>
                                Ubicación y Responsables
                            </h5>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="lugar" class="form-label">Lugar</label>
                            <input type="text" class="form-control @error('lugar') is-invalid @enderror" 
                                   id="lugar" name="lugar" value="{{ old('lugar') }}" 
                                   placeholder="Dirección o lugar del evento">
                            @error('lugar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="organo_id" class="form-label">Órgano Responsable</label>
                            <select class="form-select @error('organo_id') is-invalid @enderror" 
                                    id="organo_id" name="organo_id">
                                <option value="">Seleccionar órgano</option>
                                @foreach($organos as $organo)
                                    <option value="{{ $organo->id }}" {{ old('organo_id') == $organo->id ? 'selected' : '' }}>
                                        {{ $organo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('organo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="responsable_id" class="form-label">Responsable del Evento</label>
                            <select class="form-select @error('responsable_id') is-invalid @enderror" 
                                    id="responsable_id" name="responsable_id">
                                <option value="">Seleccionar responsable</option>
                                @foreach($miembros as $miembro)
                                    <option value="{{ $miembro->id }}" {{ old('responsable_id') == $miembro->id ? 'selected' : '' }}>
                                        {{ $miembro->nombre_completo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('responsable_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="cupo_maximo" class="form-label">Cupo Máximo</label>
                            <input type="number" class="form-control @error('cupo_maximo') is-invalid @enderror" 
                                   id="cupo_maximo" name="cupo_maximo" value="{{ old('cupo_maximo') }}" 
                                   min="1" placeholder="Número máximo de participantes">
                            @error('cupo_maximo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Configuraciones Adicionales -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="ri-settings-3-line me-2"></i>
                                Configuraciones Adicionales
                            </h5>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requiere_confirmacion" 
                                       name="requiere_confirmacion" value="1" {{ old('requiere_confirmacion') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requiere_confirmacion">
                                    Requiere confirmación de asistencia
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                      id="observaciones" name="observaciones" rows="3" 
                                      placeholder="Observaciones adicionales sobre el evento">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-start">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> Guardar Evento
                                </button>
                                <a href="{{ route('cronograma-directiva.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-close-line me-1"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de fechas
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    
    fechaInicio.addEventListener('change', function() {
        if (fechaFin.value && fechaFin.value < this.value) {
            fechaFin.value = this.value;
        }
        fechaFin.min = this.value;
    });
    
    // Validación de horas
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    
    horaInicio.addEventListener('change', function() {
        if (horaFin.value && horaFin.value <= this.value) {
            horaFin.value = '';
        }
    });
    
    // Auto-submit form on filter change
    $('#tipo_evento, #estado, #organo_id').on('change', function() {
        // Solo para filtros, no para el formulario de creación
    });
    
    // Efectos de carga para botón guardar
    $('#cronograma-form').on('submit', function() {
        const btnSubmit = $(this).find('button[type="submit"]');
        btnSubmit.addClass('loading');
        btnSubmit.html('<i class="ri-loader-4-line me-1"></i> Guardando...');
    });
});
</script>
@endsection

