@extends('partials.layouts.master')

@section('title', 'Agregar Directiva | CLDCI')
@section('title-sub', 'Gestión de Directiva')
@section('pagetitle', 'Agregar Nueva Directiva')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">
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
                        <a href="{{ route('directivas.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-group-add-line"></i>
                            Agregar Nueva Directiva
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Complete la información de la directiva para asignar un cargo a un miembro
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="directiva-form" method="POST" action="{{ route('directivas.store') }}">
                    @csrf
                    
                    <div class="row g-4">
                        <!-- Información del Miembro -->
                        <div class="col-12">
                            <h5 class="fw-semibold mb-3">
                                <i class="ri-user-line me-2"></i>Información del Miembro
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Miembro <span class="text-danger">*</span></label>
                            <select class="form-select @error('miembro_id') is-invalid @enderror" 
                                    name="miembro_id" id="miembro_id" required>
                                <option value="">Seleccionar miembro...</option>
                                @foreach($miembros as $miembro)
                                    <option value="{{ $miembro->id }}" 
                                            {{ old('miembro_id') == $miembro->id ? 'selected' : '' }}>
                                        {{ $miembro->nombre_completo }} - {{ $miembro->cedula }}
                                    </option>
                                @endforeach
                            </select>
                            @error('miembro_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Información del Cargo -->
                        <div class="col-12">
                            <h5 class="fw-semibold mb-3">
                                <i class="ri-briefcase-line me-2"></i>Información del Cargo
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Órgano <span class="text-danger">*</span></label>
                            <select class="form-select @error('organo_id') is-invalid @enderror" 
                                    name="organo_id" id="organo_id" required>
                                <option value="">Seleccionar órgano...</option>
                                @foreach($organos as $organo)
                                    <option value="{{ $organo->id }}" 
                                            {{ old('organo_id') == $organo->id ? 'selected' : '' }}>
                                        {{ $organo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('organo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cargo <span class="text-danger">*</span></label>
                            <select class="form-select @error('cargo_id') is-invalid @enderror" 
                                    name="cargo_id" id="cargo_id" required>
                                <option value="">Seleccionar cargo...</option>
                                @foreach($cargos as $cargo)
                                    <option value="{{ $cargo->id }}" 
                                            {{ old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                        {{ $cargo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cargo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Información del Período -->
                        <div class="col-12">
                            <h5 class="fw-semibold mb-3">
                                <i class="ri-calendar-line me-2"></i>Información del Período
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Período de Directiva</label>
                            <input type="text" 
                                   class="form-control @error('periodo_directiva') is-invalid @enderror" 
                                   name="periodo_directiva" 
                                   id="periodo_directiva" 
                                   value="{{ old('periodo_directiva') }}" 
                                   placeholder="Ej: 2025-2027">
                            @error('periodo_directiva')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('estado') is-invalid @enderror" 
                                    name="estado" id="estado" required>
                                <option value="">Seleccionar estado...</option>
                                <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                <option value="suspendido" {{ old('estado') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fechas -->
                        <div class="col-12">
                            <h5 class="fw-semibold mb-3">
                                <i class="ri-calendar-check-line me-2"></i>Fechas del Mandato
                            </h5>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Inicio <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                   name="fecha_inicio" 
                                   id="fecha_inicio" 
                                   value="{{ old('fecha_inicio', date('Y-m-d')) }}" 
                                   required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Fin</label>
                            <input type="date" 
                                   class="form-control @error('fecha_fin') is-invalid @enderror" 
                                   name="fecha_fin" 
                                   id="fecha_fin" 
                                   value="{{ old('fecha_fin') }}">
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Dejar vacío si no tiene fecha de fin definida</div>
                        </div>

                        <!-- Observaciones -->
                        <div class="col-12">
                            <h5 class="fw-semibold mb-3">
                                <i class="ri-file-text-line me-2"></i>Información Adicional
                            </h5>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                      name="observaciones" 
                                      id="observaciones" 
                                      rows="4" 
                                      placeholder="Ingrese observaciones adicionales sobre la directiva...">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-start gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> Guardar Directiva
                                </button>
                                <a href="{{ route('directivas.index') }}" class="btn btn-outline-secondary">
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
    // Validación del formulario
    $('#directiva-form').on('submit', function(e) {
        const miembroId = $('#miembro_id').val();
        const organoId = $('#organo_id').val();
        const cargoId = $('#cargo_id').val();
        const estado = $('#estado').val();
        const fechaInicio = $('#fecha_inicio').val();

        if (!miembroId || !organoId || !cargoId || !estado || !fechaInicio) {
            e.preventDefault();
            alert('Por favor, complete todos los campos obligatorios.');
            return false;
        }

        // Validar fecha de fin
        const fechaFin = $('#fecha_fin').val();
        if (fechaFin && fechaFin <= fechaInicio) {
            e.preventDefault();
            alert('La fecha de fin debe ser posterior a la fecha de inicio.');
            return false;
        }

        // Mostrar indicador de carga
        const btnSubmit = $(this).find('button[type="submit"]');
        btnSubmit.html('<i class="ri-loader-4-line me-1"></i> Guardando...');
        btnSubmit.prop('disabled', true);
    });

    // Auto-actualizar fecha de fin cuando cambie la fecha de inicio
    $('#fecha_inicio').on('change', function() {
        const fechaInicio = $(this).val();
        const fechaFin = $('#fecha_fin');
        
        if (fechaInicio && fechaFin.val() && fechaFin.val() <= fechaInicio) {
            fechaFin.val('');
        }
        
        // Establecer fecha mínima para fecha de fin
        fechaFin.attr('min', fechaInicio);
    });

    // Validación en tiempo real para fecha de fin
    $('#fecha_fin').on('change', function() {
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $(this).val();
        
        if (fechaInicio && fechaFin && fechaFin <= fechaInicio) {
            alert('La fecha de fin debe ser posterior a la fecha de inicio.');
            $(this).val('');
        }
    });

    // Efectos hover mejorados para botones
    $('.btn').hover(
        function() {
            $(this).addClass('shadow-sm');
        },
        function() {
            $(this).removeClass('shadow-sm');
        }
    );
});
</script>
@endsection
