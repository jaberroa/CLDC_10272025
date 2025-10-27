@extends('partials.layouts.master')

@section('title', 'Editar Cuota | CLDCI')
@section('title-sub', 'Editar Cuota')
@section('pagetitle', 'Editar Cuota')

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
                        <a href="{{ route('cuotas.index') }}" class="btn btn-volver">
                            <i class="ri-arrow-left-line"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="card-title">
                            <i class="ri-edit-line"></i>
                            Editar Cuota
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Modifique la información de la cuota según sea necesario
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                    <form action="{{ route('cuotas.update', $cuota) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="miembro_id" class="form-label">Miembro <span class="text-danger">*</span></label>
                                <select class="form-select @error('miembro_id') is-invalid @enderror" id="miembro_id" name="miembro_id" required>
                                    <option value="">Seleccionar miembro</option>
                                    @foreach($miembros as $miembro)
                                        <option value="{{ $miembro->id }}" {{ old('miembro_id', $cuota->miembro_id) == $miembro->id ? 'selected' : '' }}>
                                            {{ $miembro->nombre_completo }} ({{ $miembro->numero_carnet }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('miembro_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tipo_cuota" class="form-label">Tipo de Cuota <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipo_cuota') is-invalid @enderror" id="tipo_cuota" name="tipo_cuota" required>
                                    <option value="">Seleccionar tipo</option>
                                    @foreach($tiposCuota as $tipo)
                                        <option value="{{ $tipo }}" {{ old('tipo_cuota', $cuota->tipo_cuota) == $tipo ? 'selected' : '' }}>
                                            {{ ucfirst($tipo) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_cuota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="monto" class="form-label">Monto (RD$) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('monto') is-invalid @enderror" 
                                       id="monto" name="monto" step="0.01" min="0" 
                                       value="{{ old('monto', $cuota->monto) }}" placeholder="0.00" required>
                                @error('monto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_vencimiento" class="form-label">Vence el día <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('fecha_vencimiento') is-invalid @enderror" 
                                       id="fecha_vencimiento" name="fecha_vencimiento" 
                                       value="{{ old('fecha_vencimiento', $cuota->fecha_vencimiento ? $cuota->fecha_vencimiento->format('Y-m-d') : '') }}" required>
                                @error('fecha_vencimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-select @error('estado') is-invalid @enderror" 
                                        id="estado" name="estado" required>
                                    <option value="">Seleccionar estado</option>
                                    <option value="pendiente" {{ old('estado', $cuota->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="pagada" {{ old('estado', $cuota->estado) == 'pagada' ? 'selected' : '' }}>Pagada</option>
                                    <option value="vencida" {{ old('estado', $cuota->estado) == 'vencida' ? 'selected' : '' }}>Vencida</option>
                                    <option value="cancelada" {{ old('estado', $cuota->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="recurrente" name="recurrente" value="1" 
                                           {{ old('recurrente', $cuota->recurrente) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="recurrente">
                                        <strong>Cuota Recurrente</strong>
                                        <small class="text-muted d-block">Generar automáticamente nuevas cuotas</small>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6" id="frecuencia-container" style="display: none;">
                                <label for="frecuencia_recurrencia" class="form-label">Frecuencia de Recurrencia</label>
                                <select class="form-select @error('frecuencia_recurrencia') is-invalid @enderror" 
                                        id="frecuencia_recurrencia" name="frecuencia_recurrencia">
                                    <option value="">Seleccionar frecuencia</option>
                                    <option value="mensual" {{ old('frecuencia_recurrencia', $cuota->frecuencia_recurrencia) == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                    <option value="trimestral" {{ old('frecuencia_recurrencia', $cuota->frecuencia_recurrencia) == 'trimestral' ? 'selected' : '' }}>Trimestral</option>
                                    <option value="anual" {{ old('frecuencia_recurrencia', $cuota->frecuencia_recurrencia) == 'anual' ? 'selected' : '' }}>Anual</option>
                                </select>
                                @error('frecuencia_recurrencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="proxima-fecha-container" style="display: none;">
                                <label for="proxima_fecha_generacion" class="form-label">Próxima Fecha de Generación</label>
                                <input type="date" class="form-control @error('proxima_fecha_generacion') is-invalid @enderror" 
                                       id="proxima_fecha_generacion" name="proxima_fecha_generacion" 
                                       value="{{ old('proxima_fecha_generacion', $cuota->proxima_fecha_generacion ? $cuota->proxima_fecha_generacion->format('Y-m-d') : '') }}">
                                @error('proxima_fecha_generacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                          id="observaciones" name="observaciones" rows="3" 
                                          placeholder="Notas adicionales sobre la cuota...">{{ old('observaciones', $cuota->observaciones) }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('cuotas.index') }}" class="btn btn-secondary">
                                        <i class="ri-close-line me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i> Actualizar Cuota
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // No establecer fecha mínima para edición
    // El usuario puede editar fechas pasadas si es necesario
    
    // Manejar checkbox de recurrencia
    const checkboxRecurrente = document.getElementById('recurrente');
    const frecuenciaContainer = document.getElementById('frecuencia-container');
    const proximaFechaContainer = document.getElementById('proxima-fecha-container');
    
    function toggleRecurrenceFields() {
        if (checkboxRecurrente.checked) {
            frecuenciaContainer.style.display = 'block';
            proximaFechaContainer.style.display = 'block';
        } else {
            frecuenciaContainer.style.display = 'none';
            proximaFechaContainer.style.display = 'none';
        }
    }
    
    checkboxRecurrente.addEventListener('change', toggleRecurrenceFields);
    
    // Inicializar estado basado en el valor actual
    toggleRecurrenceFields();
});
</script>
@endsection
