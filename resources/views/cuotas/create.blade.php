@extends('partials.layouts.master')

@section('title', 'Crear Cuota | CLDCI')
@section('title-sub', 'Nueva Cuota')
@section('pagetitle', 'Crear Cuota')

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
                            <i class="ri-add-line"></i>
                            Crear Nueva Cuota
                        </h4>
                        <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                            Complete la información de la cuota para registrarla en el sistema
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                    <form action="{{ route('cuotas.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="miembro_id" class="form-label">Miembro <span class="text-danger">*</span></label>
                                <select class="form-select @error('miembro_id') is-invalid @enderror" id="miembro_id" name="miembro_id" required>
                                    <option value="">Seleccionar miembro</option>
                                    @foreach($miembros as $miembro)
                                        <option value="{{ $miembro->id }}" {{ old('miembro_id') == $miembro->id ? 'selected' : '' }}>
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
                                        <option value="{{ $tipo }}" {{ old('tipo_cuota') == $tipo ? 'selected' : '' }}>
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
                                       value="{{ old('monto') }}" placeholder="0.00" required>
                                @error('monto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_vencimiento" class="form-label">Vence el día <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('fecha_vencimiento') is-invalid @enderror" 
                                       id="fecha_vencimiento" name="fecha_vencimiento" 
                                       value="{{ old('fecha_vencimiento') }}" required>
                                @error('fecha_vencimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                          id="observaciones" name="observaciones" rows="3" 
                                          placeholder="Notas adicionales sobre la cuota...">{{ old('observaciones') }}</textarea>
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
                                        <i class="ri-save-line me-1"></i> Crear Cuota
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
    // Establecer fecha mínima como mañana
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('fecha_vencimiento').min = tomorrow.toISOString().split('T')[0];
});
</script>
@endsection
