<form action="{{ route('asambleas.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <!-- Información Básica -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-information-line me-2"></i>
                Información Básica
            </h5>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título de la Asamblea <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                       id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                @error('titulo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de Asamblea <span class="text-danger">*</span></label>
                <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                    <option value="">Seleccione el tipo</option>
                    <option value="ordinaria" {{ old('tipo') == 'ordinaria' ? 'selected' : '' }}>Ordinaria</option>
                    <option value="extraordinaria" {{ old('tipo') == 'extraordinaria' ? 'selected' : '' }}>Extraordinaria</option>
                    <option value="especial" {{ old('tipo') == 'especial' ? 'selected' : '' }}>Especial</option>
                </select>
                @error('tipo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                          id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Fechas y Horarios -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-calendar-line me-2"></i>
                Fechas y Horarios
            </h5>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="fecha_convocatoria" class="form-label">Fecha de Convocatoria <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control @error('fecha_convocatoria') is-invalid @enderror" 
                       id="fecha_convocatoria" name="fecha_convocatoria" 
                       value="{{ old('fecha_convocatoria', now()->format('Y-m-d\TH:i')) }}" required>
                @error('fecha_convocatoria')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="fecha_asamblea" class="form-label">Fecha de la Asamblea <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control @error('fecha_asamblea') is-invalid @enderror" 
                       id="fecha_asamblea" name="fecha_asamblea" 
                       value="{{ old('fecha_asamblea', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required>
                @error('fecha_asamblea')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Ubicación -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-map-pin-line me-2"></i>
                Ubicación
            </h5>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="lugar" class="form-label">Lugar <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('lugar') is-invalid @enderror" 
                       id="lugar" name="lugar" value="{{ old('lugar') }}" required>
                @error('lugar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="modalidad" class="form-label">Modalidad <span class="text-danger">*</span></label>
                <select class="form-select @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad" required>
                    <option value="">Seleccione la modalidad</option>
                    <option value="presencial" {{ old('modalidad') == 'presencial' ? 'selected' : '' }}>Presencial</option>
                    <option value="virtual" {{ old('modalidad') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                    <option value="hibrida" {{ old('modalidad') == 'hibrida' ? 'selected' : '' }}>Híbrida</option>
                </select>
                @error('modalidad')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-12" id="enlace-virtual-container" style="display: none;">
            <div class="mb-3">
                <label for="enlace_virtual" class="form-label">Enlace Virtual</label>
                <input type="url" class="form-control @error('enlace_virtual') is-invalid @enderror" 
                       id="enlace_virtual" name="enlace_virtual" value="{{ old('enlace_virtual') }}">
                @error('enlace_virtual')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Quorum y Asistencia -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-group-line me-2"></i>
                Quorum y Asistencia
            </h5>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="quorum_minimo" class="form-label">Quorum Mínimo <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('quorum_minimo') is-invalid @enderror" 
                       id="quorum_minimo" name="quorum_minimo" value="{{ old('quorum_minimo', 1) }}" 
                       min="1" required>
                @error('quorum_minimo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="organizacion_id" class="form-label">Organización <span class="text-danger">*</span></label>
                <select class="form-select @error('organizacion_id') is-invalid @enderror" id="organizacion_id" name="organizacion_id" required>
                    <option value="">Seleccione la organización</option>
                    <option value="1" {{ old('organizacion_id') == '1' ? 'selected' : '' }}>CLDCI</option>
                </select>
                @error('organizacion_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-{{ $alignButtons ?? 'end' }} gap-2">
                <a href="{{ route('asambleas.proxima') }}" class="{{ $cancelClass ?? 'btn btn-outline-secondary' }}">
                    <i class="{{ $cancelIcon ?? 'ri-close-line' }} me-1"></i>
                    {{ $cancelLabel ?? 'Cancelar' }}
                </a>
                <button type="submit" class="{{ $submitClass ?? 'btn btn-primary' }}">
                    <i class="{{ $submitIcon ?? 'ri-save-line' }} me-1"></i>
                    {{ $submitLabel ?? 'Crear Asamblea' }}
                </button>
            </div>
        </div>
    </div>
</form>

