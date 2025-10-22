@extends('partials.layouts.master')

@section('title', 'Crear Directiva')
@section('title-sub', 'Directiva')
@section('pagetitle', 'Crear Nuevo Período de Directiva')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-shrink-0">
                        <h4 class="card-title mb-0">
                            <i class="ri-user-add-line me-2"></i>
                            Información del Período
                        </h4>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('directiva.index') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i>
                            <span>Volver a Directiva</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('directiva.store') }}" method="POST" id="directivaForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="organizacion_id" class="form-label">Organización <span class="text-danger">*</span></label>
                                <select class="form-select" id="organizacion_id" name="organizacion_id" required>
                                    <option value="">Seleccionar organización</option>
                                    @foreach($organizaciones as $organizacion)
                                        <option value="{{ $organizacion->id }}" 
                                                {{ old('organizacion_id') == $organizacion->id ? 'selected' : '' }}>
                                            {{ $organizacion->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('organizacion_id')
                                    <div class="text-danger fs-12">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                       value="{{ old('fecha_inicio') }}" required>
                                @error('fecha_inicio')
                                    <div class="text-danger fs-12">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                       value="{{ old('fecha_fin') }}" required>
                                @error('fecha_fin')
                                    <div class="text-danger fs-12">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="ri-user-star-line me-2"></i>
                                Composición de la Directiva
                            </h5>
                        </div>
                    </div>

                    <div id="directiva-container">
                        <div class="directiva-item mb-3 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Cargo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="directiva[0][cargo]" 
                                           placeholder="Ej: Presidente" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Miembro <span class="text-danger">*</span></label>
                                    <select class="form-select" name="directiva[0][miembro_id]" required>
                                        <option value="">Seleccionar miembro</option>
                                        @foreach($miembros as $miembro)
                                            <option value="{{ $miembro->id }}">
                                                {{ $miembro->nombre_completo }} - {{ $miembro->organizacion->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-directiva-item" 
                                            style="display: none;">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-primary" id="add-directiva-item">
                                <i class="ri-add-line me-1"></i>
                                Agregar Cargo
                            </button>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>
                                    Crear Período
                                </button>
                                <a href="{{ route('directiva.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-close-line me-1"></i>
                                    Cancelar
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
    let directivaIndex = 1;

    // Agregar nuevo cargo
    document.getElementById('add-directiva-item').addEventListener('click', function() {
        const container = document.getElementById('directiva-container');
        const template = document.querySelector('.directiva-item').cloneNode(true);
        
        // Actualizar índices
        template.querySelectorAll('input, select').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${directivaIndex}]`));
            }
        });
        
        // Limpiar valores
        template.querySelectorAll('input, select').forEach(input => {
            input.value = '';
        });
        
        // Mostrar botón eliminar
        template.querySelector('.remove-directiva-item').style.display = 'block';
        
        container.appendChild(template);
        directivaIndex++;
    });

    // Eliminar cargo
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-directiva-item')) {
            e.target.closest('.directiva-item').remove();
        }
    });

    // Validación de fechas
    document.getElementById('fecha_inicio').addEventListener('change', function() {
        const fechaInicio = new Date(this.value);
        const fechaFinInput = document.getElementById('fecha_fin');
        
        if (fechaInicio && fechaFinInput.value) {
            const fechaFin = new Date(fechaFinInput.value);
            if (fechaFin <= fechaInicio) {
                fechaFinInput.setCustomValidity('La fecha fin debe ser posterior a la fecha inicio');
            } else {
                fechaFinInput.setCustomValidity('');
            }
        }
    });

    document.getElementById('fecha_fin').addEventListener('change', function() {
        const fechaFin = new Date(this.value);
        const fechaInicioInput = document.getElementById('fecha_inicio');
        
        if (fechaFin && fechaInicioInput.value) {
            const fechaInicio = new Date(fechaInicioInput.value);
            if (fechaFin <= fechaInicio) {
                this.setCustomValidity('La fecha fin debe ser posterior a la fecha inicio');
            } else {
                this.setCustomValidity('');
            }
        }
    });
});
</script>
@endsection


