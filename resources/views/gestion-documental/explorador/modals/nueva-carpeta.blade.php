<div class="modal fade" id="modalNuevaCarpeta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-folder-add-line me-2"></i>
                    Nueva Carpeta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevaCarpeta" onsubmit="crearCarpeta(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la carpeta</label>
                        <input type="text" class="form-control" name="nombre" placeholder="Ej: Documentos 2025" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="color" id="color1" value="#FFC107" checked>
                            <label class="btn btn-sm" for="color1" style="background: #FFC107; width: 40px; height: 40px;"></label>
                            
                            <input type="radio" class="btn-check" name="color" id="color2" value="#0d6efd">
                            <label class="btn btn-sm" for="color2" style="background: #0d6efd; width: 40px; height: 40px;"></label>
                            
                            <input type="radio" class="btn-check" name="color" id="color3" value="#198754">
                            <label class="btn btn-sm" for="color3" style="background: #198754; width: 40px; height: 40px;"></label>
                            
                            <input type="radio" class="btn-check" name="color" id="color4" value="#dc3545">
                            <label class="btn btn-sm" for="color4" style="background: #dc3545; width: 40px; height: 40px;"></label>
                            
                            <input type="radio" class="btn-check" name="color" id="color5" value="#6f42c1">
                            <label class="btn btn-sm" for="color5" style="background: #6f42c1; width: 40px; height: 40px;"></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i>
                        Crear Carpeta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function crearCarpeta(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = {
            nombre: formData.get('nombre'),
            color: formData.get('color'),
            carpeta_padre_id: '{{ request()->carpeta_id ?? "" }}',
            seccion_id: '{{ $carpetaActual->seccion_id ?? request()->seccion_id ?? 1 }}'
        };
        
        fetch('{{ route("gestion-documental.explorador.crear-carpeta") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showSuccessToast(data.message);
                $('#modalNuevaCarpeta').modal('hide');
                location.reload();
            }
        })
        .catch(error => {
            showErrorToast('Error al crear la carpeta');
        });
    }
</script>

