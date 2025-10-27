@props(['miembro'])

<tr>
    <td>
        <div class="form-check">
            <input class="form-check-input member-checkbox" type="checkbox" 
                   value="{{ $miembro->id }}" 
                   id="member_{{ $miembro->id }}"
                   onchange="updateSelectAllState()">
            <label class="form-check-label" for="member_{{ $miembro->id }}">
                <i class="ri-checkbox-line"></i>
            </label>
        </div>
    </td>
    
    <!-- Columna de Miembro -->
    <td>
        <x-miembros.miembro-avatar :miembro="$miembro" />
    </td>
    
    <!-- Columna de Tipo de Membresía -->
    <td>
        <x-miembros.tipo-membresia :tipo="$miembro->tipo_membresia" />
    </td>
    
    <!-- Columna de Estado -->
    <td>
        <x-miembros.estado-badge :estado="$miembro->estadoMembresia" />
    </td>
    
    <!-- Columna de Organización -->
    <td>
        <span class="fw-semibold">{{ Str::limit($miembro->organizacion->nombre, 20) }}</span>
    </td>
    
    <!-- Columna de Fecha Ingreso -->
    <td>
        <span class="fw-semibold">{{ $miembro->fecha_ingreso->format('d/m/Y') }}</span>
    </td>
    
    <!-- Columna de Acciones -->
    <td>
        <x-miembros.acciones-column :miembro="$miembro" />
    </td>
</tr>
