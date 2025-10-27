<div class="modal fade" id="modalSubirArchivo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-upload-line me-2"></i>
                    Subir Archivos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSubirArchivo" onsubmit="subirArchivo(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Seleccionar archivos</label>
                        <input type="file" class="form-control" id="inputArchivos" multiple onchange="mostrarArchivos()">
                        <small class="text-muted">Puedes seleccionar múltiples archivos</small>
                    </div>
                    
                    <div id="listaArchivos"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubir" disabled>
                        <i class="ri-upload-line me-1"></i>
                        Subir Archivos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let archivosSeleccionados = [];
    
    function mostrarArchivos() {
        const input = document.getElementById('inputArchivos');
        archivosSeleccionados = Array.from(input.files);
        
        if (archivosSeleccionados.length === 0) {
            document.getElementById('listaArchivos').innerHTML = '';
            document.getElementById('btnSubir').disabled = true;
            return;
        }
        
        let html = '<div class="list-group">';
        archivosSeleccionados.forEach((file, index) => {
            const tamano = (file.size / 1024).toFixed(1);
            html += `
                <div class="list-group-item d-flex align-items-center">
                    <i class="ri-file-line fs-4 me-3"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${file.name}</div>
                        <small class="text-muted">${tamano} KB</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="eliminarArchivo(${index})">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            `;
        });
        html += '</div>';
        
        document.getElementById('listaArchivos').innerHTML = html;
        document.getElementById('btnSubir').disabled = false;
    }
    
    function eliminarArchivo(index) {
        archivosSeleccionados.splice(index, 1);
        const input = document.getElementById('inputArchivos');
        const dt = new DataTransfer();
        archivosSeleccionados.forEach(file => dt.items.add(file));
        input.files = dt.files;
        mostrarArchivos();
    }
    
    function subirArchivo(e) {
        e.preventDefault();
        
        if (archivosSeleccionados.length === 0) return;
        
        const btnSubir = document.getElementById('btnSubir');
        btnSubir.disabled = true;
        btnSubir.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Subiendo...';
        
        let completados = 0;
        
        archivosSeleccionados.forEach(file => {
            const formData = new FormData();
            formData.append('archivo', file);
            formData.append('carpeta_id', '{{ request()->carpeta_id ?? "" }}');
            formData.append('seccion_id', '{{ $carpetaActual->seccion_id ?? request()->seccion_id ?? 1 }}');
            
            fetch('{{ route("gestion-documental.explorador.subir-archivo") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                completados++;
                if (completados === archivosSeleccionados.length) {
                    showSuccessToast(`${completados} archivo(s) subido(s) exitosamente`);
                    $('#modalSubirArchivo').modal('hide');
                    location.reload();
                }
            })
            .catch(error => {
                showErrorToast(`Error al subir ${file.name}`);
                btnSubir.disabled = false;
                btnSubir.innerHTML = '<i class="ri-upload-line me-1"></i> Subir Archivos';
            });
        });
    }
</script>

