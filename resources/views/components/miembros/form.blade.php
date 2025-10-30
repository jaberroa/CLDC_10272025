@props([
    'organizaciones' => [],
    'estadosMembresia' => [],
    'tiposMembresia' => [],
    'miembro' => null,
    'method' => 'POST',
    'action' => null,
    'submitLabel' => 'Guardar',
    'submitIcon' => 'ri-save-line',
    'submitClass' => 'btn btn-primary',
    'cancelLabel' => 'Cancelar',
    'cancelIcon' => 'ri-close-line',
    'cancelClass' => 'btn btn-outline-secondary',
    'alignButtons' => 'start'
])

@php
    $isEdit = $miembro !== null;
    $formAction = $action ?? ($isEdit ? route('miembros.update', $miembro->id) : route('miembros.store'));
    $formMethod = $isEdit ? 'PUT' : 'POST';
    // Derivar nombre y apellido desde nombre_completo cuando editemos y no existan campos separados
    $derivedNombre = '';
    $derivedApellido = '';
    if ($isEdit && empty($miembro->nombre) && empty($miembro->apellido)) {
        $full = trim($miembro->nombre_completo ?? '');
        if ($full !== '') {
            $parts = preg_split('/\s+/', $full);
            $derivedNombre = array_shift($parts) ?? '';
            $derivedApellido = implode(' ', $parts);
        }
    }
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="miembro-form">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    
    {{ $extra ?? '' }}

    <!-- Información Personal -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-user-line me-2"></i>
                Información Personal
            </h5>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                   id="nombre" name="nombre" value="{{ old('nombre', $miembro->nombre ?? $derivedNombre) }}" required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                   id="apellido" name="apellido" value="{{ old('apellido', $miembro->apellido ?? $derivedApellido) }}" required>
            @error('apellido')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('cedula') is-invalid @enderror" 
                   id="cedula" name="cedula" value="{{ old('cedula', $miembro->cedula ?? '') }}" required>
            @error('cedula')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email', $miembro->email ?? '') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control @error('telefono') is-invalid @enderror" 
                   id="telefono" name="telefono" value="{{ old('telefono', $miembro->telefono ?? '') }}">
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="profesion" class="form-label">Profesión</label>
            <input type="text" class="form-control @error('profesion') is-invalid @enderror" 
                   id="profesion" name="profesion" value="{{ old('profesion', $miembro->profesion ?? '') }}">
            @error('profesion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Información de Membresía -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-group-line me-2"></i>
                Información de Membresía
            </h5>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="estado_membresia_id" class="form-label">Estado de Membresía <span class="text-danger">*</span></label>
            <div class="input-group">
                <select class="form-select @error('estado_membresia_id') is-invalid @enderror" 
                        id="estado_membresia_id" name="estado_membresia_id" required>
                    <option value="">Seleccionar estado</option>
                    @foreach($estadosMembresia as $estado)
                        <option value="{{ $estado->id }}" {{ old('estado_membresia_id', $miembro->estado_membresia_id ?? '') == $estado->id ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-outline-primary" type="button" id="btnAddEstadoMembresia" title="Agregar Estado">
                    <i class="ri-add-line"></i>
                </button>
                <button class="btn btn-outline-danger" type="button" id="btnDeleteEstadoMembresia" title="Eliminar Estado">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
            @error('estado_membresia_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="tipo_membresia" class="form-label">Tipo de Membresía <span class="text-danger">*</span></label>
            <div class="input-group">
                <select class="form-select @error('tipo_membresia') is-invalid @enderror" 
                        id="tipo_membresia" name="tipo_membresia" required>
                    <option value="">Seleccionar tipo</option>
                    @php
                        $tipoActual = old('tipo_membresia', $miembro->tipo_membresia ?? '');
                        $tiposLista = isset($tiposMembresia) ? $tiposMembresia->map(fn($t)=>[$t->slug,$t->nombre])->toArray() : [];
                        $tiposDefault = [
                            ['fundador','Fundador'],['activo','Activo'],['pasivo','Pasivo'],['honorifico','Honorífico'],['estudiante','Estudiante'],['diaspora','Diáspora']
                        ];
                        // Fusionar por slug evitando duplicados
                        $slugs = [];
                        $tipos = [];
                        foreach (array_merge($tiposLista, $tiposDefault) as $pair) {
                            [$slug,$nombre] = $pair;
                            if (!in_array($slug, $slugs)) { $slugs[]=$slug; $tipos[]=['slug'=>$slug,'nombre'=>$nombre]; }
                        }
                    @endphp
                    @foreach($tipos as $t)
                        <option value="{{ $t['slug'] }}" {{ $tipoActual === $t['slug'] ? 'selected' : '' }}>{{ $t['nombre'] }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-primary" type="button" id="btnAddTipoMembresia" title="Agregar Tipo">
                    <i class="ri-add-line"></i>
                </button>
                <button class="btn btn-outline-danger" type="button" id="btnDeleteTipoMembresia" title="Eliminar Tipo">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
            @error('tipo_membresia')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="organizacion_id" class="form-label">Organización <span class="text-danger">*</span></label>
            <select class="form-select @error('organizacion_id') is-invalid @enderror" 
                    id="organizacion_id" name="organizacion_id" required>
                <option value="">Seleccionar organización</option>
                @foreach($organizaciones as $organizacion)
                    <option value="{{ $organizacion->id }}" {{ old('organizacion_id', $miembro->organizacion_id ?? '') == $organizacion->id ? 'selected' : '' }}>
                        {{ $organizacion->nombre }}
                    </option>
                @endforeach
            </select>
            @error('organizacion_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="fecha_ingreso" class="form-label">Fecha de Ingreso <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('fecha_ingreso') is-invalid @enderror" 
                   id="fecha_ingreso" name="fecha_ingreso" 
                   value="{{ old('fecha_ingreso', $miembro && $miembro->fecha_ingreso ? $miembro->fecha_ingreso->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
            @error('fecha_ingreso')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Foto del Miembro -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="ri-camera-line me-2"></i>
                Foto del Miembro
            </h5>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="foto" class="form-label">Foto</label>
            <input type="file" class="form-control @error('foto') is-invalid @enderror" 
                   id="foto" name="foto" accept="image/*">
            <div class="form-text">
                <i class="ri-information-line me-1"></i>
                Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB
            </div>
            @error('foto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <div class="d-flex align-items-center h-100">
                <div class="text-center">
                    <div class="avatar-lg mx-auto mb-2 bg-light rounded-circle d-flex align-items-center justify-content-center">
                        @if($miembro && $miembro->foto_url)
                            <img src="{{ asset('storage/' . $miembro->foto_url) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" alt="Foto actual">
                        @else
                            <i class="ri-user-line fs-24 text-muted"></i>
                        @endif
                    </div>
                    <small class="text-muted">Vista previa de la foto</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex gap-2 justify-content-{{ $alignButtons }}">
                <button type="submit" class="{{ $submitClass }}">
                    <i class="{{ $submitIcon }} me-1"></i> {{ $submitLabel }}
                </button>
                <a href="{{ route('miembros.index') }}" class="{{ $cancelClass }}">
                    <i class="{{ $cancelIcon }} me-1"></i> {{ $cancelLabel }}
                </a>
            </div>
        </div>
    </div>
</form>

<!-- Modal: Eliminar Estado (Admin) -->
<div class="modal fade" id="modalEliminarEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-subtle">
                <h5 class="modal-title"><i class="ri-delete-bin-line me-2"></i>Eliminar Estado de Membresía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Para eliminar el estado seleccione el estado y escriba su nombre exactamente como aparece.</p>
                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="estado_a_eliminar"></select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirme el nombre del estado</label>
                    <input type="text" class="form-control" id="confirm_nombre_estado" placeholder="Escriba el nombre exacto">
                    <div class="form-text text-muted">
                        Para eliminar este estado escriba exactamente: "<span id="estado_confirm_hint">Seleccione un estado</span>"
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="force_delete_estado">
                    <label class="form-check-label" for="force_delete_estado">
                        Forzar (poner en null el estado de los miembros afectados)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmEliminarEstado">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Eliminar Tipo (Admin) -->
<div class="modal fade" id="modalEliminarTipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-subtle">
                <h5 class="modal-title"><i class="ri-delete-bin-line me-2"></i>Eliminar Tipo de Membresía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Para eliminar el tipo seleccione el tipo y escriba su nombre exactamente como aparece.</p>
                <div class="mb-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" id="tipo_a_eliminar"></select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirme el nombre del tipo</label>
                    <input type="text" class="form-control" id="confirm_nombre_tipo" placeholder="Escriba el nombre exacto">
                    <div class="form-text text-muted">
                        Para eliminar este tipo escriba exactamente: "<span id="tipo_confirm_hint">Seleccione un tipo</span>"
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="force_delete_tipo">
                    <label class="form-check-label" for="force_delete_tipo">
                        Forzar (poner en null el tipo de los miembros afectados)
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirm_delete_tipo">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nuevo Estado de Membresía -->
<div class="modal fade" id="modalNuevoEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title"><i class="ri-add-line me-2"></i>Nuevo Estado de Membresía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="estado_nombre" placeholder="Ej: activa" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción (opcional)</label>
                    <textarea class="form-control" id="estado_descripcion" rows="2" placeholder="Descripción..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEstado">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nuevo Tipo de Membresía (solo UI) -->
<div class="modal fade" id="modalNuevoTipoMembresia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title"><i class="ri-add-line me-2"></i>Nuevo Tipo de Membresía</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="tipo_nombre" placeholder="Ej: vitalicio" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarTipo">Guardar</button>
            </div>
        </div>
    </div>
</div>