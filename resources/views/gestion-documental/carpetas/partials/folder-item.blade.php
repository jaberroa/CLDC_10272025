<li id="folder-{{ $carpeta->id }}">
    <div class="folder-item {{ $carpeta->subcarpetas->count() > 0 ? 'has-children' : '' }}">
        @if($carpeta->subcarpetas->count() > 0)
            <div class="folder-toggle" onclick="toggleFolder({{ $carpeta->id }})">
                <i class="ri-arrow-right-s-line"></i>
            </div>
        @endif
        
        <div class="folder-info" onclick="abrirCarpeta({{ $carpeta->id }})">
            <i class="ri-folder-fill folder-icon" style="color: {{ $carpeta->color ?? '#FFC107' }}"></i>
            <div class="folder-name">
                {{ $carpeta->nombre }}
                <span class="nivel-badge ms-2">Nivel {{ $carpeta->nivel }}</span>
            </div>
            <div class="folder-meta">
                <i class="ri-folder-line me-1"></i>
                {{ $carpeta->subcarpetas->count() }} subcarpetas
                <span class="mx-2">•</span>
                <i class="ri-file-line me-1"></i>
                {{ $carpeta->documentos->count() }} archivos
            </div>
        </div>
        
        <div class="folder-actions" onclick="event.stopPropagation()">
            <div class="btn-group btn-group-sm">
                <button class="btn btn-soft-primary" onclick="mostrarModalNuevaCarpeta({{ $carpeta->id }})" title="Crear subcarpeta">
                    <i class="ri-folder-add-line"></i>
                </button>
                <button class="btn btn-soft-info" onclick="abrirCarpeta({{ $carpeta->id }})" title="Abrir">
                    <i class="ri-folder-open-line"></i>
                </button>
                <button class="btn btn-soft-warning" onclick="window.location='{{ route('gestion-documental.carpetas.edit', $carpeta) }}'" title="Editar">
                    <i class="ri-edit-line"></i>
                </button>
                <button class="btn btn-soft-danger" onclick="eliminarCarpeta({{ $carpeta->id }}, '{{ $carpeta->nombre }}')" title="Eliminar">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
    </div>
    
    @if($carpeta->subcarpetas->count() > 0)
        <ul>
            @foreach($carpeta->subcarpetas as $subcarpeta)
                @include('gestion-documental.carpetas.partials.folder-item', ['carpeta' => $subcarpeta])
            @endforeach
        </ul>
    @endif
</li>

