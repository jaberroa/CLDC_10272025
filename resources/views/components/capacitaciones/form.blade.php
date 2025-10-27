@props([
    'capacitacion' => null,
    'submitLabel' => 'Guardar Capacitación',
    'submitIcon' => 'ri-save-line',
    'submitClass' => 'btn btn-primary',
    'cancelLabel' => 'Cancelar',
    'cancelIcon' => 'ri-close-line',
    'cancelClass' => 'btn btn-outline-secondary',
    'alignButtons' => 'start'
])

<form action="{{ $capacitacion ? route('capacitaciones.update', $capacitacion) : route('capacitaciones.store') }}" method="POST" enctype="multipart/form-data">
    @if($capacitacion)
        @method('PUT')
    @endif
    @csrf

    <div class="row g-3">
        <!-- Título -->
        <div class="col-md-6">
            <label for="titulo" class="form-label fw-semibold">
                <i class="ri-book-line me-1"></i>
                Título del Curso <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('titulo') is-invalid @enderror" 
                   id="titulo" 
                   name="titulo" 
                   value="{{ old('titulo', $capacitacion?->titulo) }}" 
                   placeholder="Ingrese el título del curso"
                   required>
            @error('titulo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Modalidad -->
        <div class="col-md-6">
            <label for="modalidad" class="form-label fw-semibold">
                <i class="ri-computer-line me-1"></i>
                Modalidad <span class="text-danger">*</span>
            </label>
            <select class="form-select @error('modalidad') is-invalid @enderror" 
                    id="modalidad" 
                    name="modalidad" 
                    required>
                <option value="">Seleccionar modalidad</option>
                <option value="presencial" {{ old('modalidad', $capacitacion?->modalidad) == 'presencial' ? 'selected' : '' }}>
                    Presencial
                </option>
                <option value="virtual" {{ old('modalidad', $capacitacion?->modalidad) == 'virtual' ? 'selected' : '' }}>
                    Virtual
                </option>
                <option value="mixta" {{ old('modalidad', $capacitacion?->modalidad) == 'mixta' ? 'selected' : '' }}>
                    Mixta
                </option>
            </select>
            @error('modalidad')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Fecha de Inicio -->
        <div class="col-md-6">
            <label for="fecha_inicio" class="form-label fw-semibold">
                <i class="ri-calendar-line me-1"></i>
                Fecha de Inicio <span class="text-danger">*</span>
            </label>
            <input type="date" 
                   class="form-control @error('fecha_inicio') is-invalid @enderror" 
                   id="fecha_inicio" 
                   name="fecha_inicio" 
                   value="{{ old('fecha_inicio', $capacitacion?->fecha_inicio?->format('Y-m-d')) }}" 
                   required>
            @error('fecha_inicio')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Fecha de Finalización -->
        <div class="col-md-6">
            <label for="fecha_fin" class="form-label fw-semibold">
                <i class="ri-calendar-check-line me-1"></i>
                Fecha de Finalización
            </label>
            <input type="date" 
                   class="form-control @error('fecha_fin') is-invalid @enderror" 
                   id="fecha_fin" 
                   name="fecha_fin" 
                   value="{{ old('fecha_fin', $capacitacion?->fecha_fin?->format('Y-m-d')) }}">
            @error('fecha_fin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Lugar -->
        <div class="col-md-6">
            <label for="lugar" class="form-label fw-semibold">
                <i class="ri-map-pin-line me-1"></i>
                Lugar
            </label>
            <input type="text" 
                   class="form-control @error('lugar') is-invalid @enderror" 
                   id="lugar" 
                   name="lugar" 
                   value="{{ old('lugar', $capacitacion?->lugar) }}" 
                   placeholder="Ingrese el lugar del curso">
            @error('lugar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Enlace Virtual (condicional) -->
        <div class="col-md-6" id="enlace-virtual-container" style="display: none;">
            <label for="enlace_virtual" class="form-label fw-semibold">
                <i class="ri-links-line me-1"></i>
                Enlace Virtual
            </label>
            <input type="url" 
                   class="form-control @error('enlace_virtual') is-invalid @enderror" 
                   id="enlace_virtual" 
                   name="enlace_virtual" 
                   value="{{ old('enlace_virtual', $capacitacion?->enlace_virtual) }}" 
                   placeholder="https://meet.google.com/...">
            @error('enlace_virtual')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Costo -->
        <div class="col-md-6">
            <label for="costo" class="form-label fw-semibold">
                <i class="ri-money-dollar-circle-line me-1"></i>
                Costo (RD$) <span class="text-danger">*</span>
            </label>
            <input type="number" 
                   class="form-control @error('costo') is-invalid @enderror" 
                   id="costo" 
                   name="costo" 
                   value="{{ old('costo', $capacitacion?->costo) }}" 
                   step="0.01" 
                   min="0" 
                   placeholder="0.00"
                   required>
            @error('costo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Cupo Máximo -->
        <div class="col-md-6">
            <label for="cupo_maximo" class="form-label fw-semibold">
                <i class="ri-group-line me-1"></i>
                Cupo Máximo
            </label>
            <input type="number" 
                   class="form-control @error('cupo_maximo') is-invalid @enderror" 
                   id="cupo_maximo" 
                   name="cupo_maximo" 
                   value="{{ old('cupo_maximo', $capacitacion?->cupo_maximo) }}" 
                   min="1" 
                   placeholder="Ej: 20">
            @error('cupo_maximo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Instructor -->
        <div class="col-md-6">
            <label for="instructor" class="form-label fw-semibold">
                <i class="ri-user-line me-1"></i>
                Instructor
            </label>
            <input type="text" 
                   class="form-control @error('instructor') is-invalid @enderror" 
                   id="instructor" 
                   name="instructor" 
                   value="{{ old('instructor', $capacitacion?->instructor) }}" 
                   placeholder="Nombre del instructor">
            @error('instructor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Estado Activo -->
        <div class="col-md-6">
            <label for="activo" class="form-label fw-semibold">
                <i class="ri-toggle-line me-1"></i>
                Estado
            </label>
            <select class="form-select @error('activo') is-invalid @enderror" 
                    id="activo" 
                    name="activo">
                <option value="1" {{ old('activo', $capacitacion?->activo ?? true) == '1' ? 'selected' : '' }}>
                    Activo
                </option>
                <option value="0" {{ old('activo', $capacitacion?->activo ?? true) == '0' ? 'selected' : '' }}>
                    Inactivo
                </option>
            </select>
            @error('activo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Descripción -->
        <div class="col-12">
            <label for="descripcion" class="form-label fw-semibold">
                <i class="ri-file-text-line me-1"></i>
                Descripción
            </label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                      id="descripcion" 
                      name="descripcion" 
                      rows="4" 
                      placeholder="Descripción detallada del curso...">{{ old('descripcion', $capacitacion?->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Contenido -->
        <div class="col-12">
            <label for="contenido" class="form-label fw-semibold">
                <i class="ri-book-open-line me-1"></i>
                Contenido del Curso
            </label>
            <textarea class="form-control @error('contenido') is-invalid @enderror" 
                      id="contenido" 
                      name="contenido" 
                      rows="4" 
                      placeholder="Temario y contenido del curso...">{{ old('contenido', $capacitacion?->contenido) }}</textarea>
            @error('contenido')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-{{ $alignButtons }} gap-2">
                <button type="submit" class="{{ $submitClass }}">
                    <i class="{{ $submitIcon }} me-1"></i>
                    {{ $submitLabel }}
                </button>
                <a href="{{ route('capacitaciones.index') }}" class="{{ $cancelClass }}">
                    <i class="{{ $cancelIcon }} me-1"></i>
                    {{ $cancelLabel }}
                </a>
            </div>
        </div>
    </div>
</form>
