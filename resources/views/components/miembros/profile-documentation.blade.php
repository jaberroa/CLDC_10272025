<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="ri-file-text-line me-2"></i>Documentación
        </h5>
    </div>
    <div class="card-body">
        <!-- Zona de carga de archivos -->
        <div class="upload-area mb-4">
            <div class="dropzone" id="documentationDropzone">
                <div class="dz-message">
                    <div class="text-center">
                        <i class="ri-upload-cloud-2-line fs-48 text-muted mb-3"></i>
                        <h6 class="mb-2">Arrastra y suelta archivos aquí</h6>
                        <p class="text-muted mb-3">o haz clic para seleccionar archivos</p>
                        <button type="button" class="btn btn-primary btn-sm" id="selectFilesBtn">
                            <i class="ri-add-line me-1"></i>Seleccionar Archivos
                        </button>
                    </div>
                </div>
            </div>
            <!-- Input file oculto como respaldo -->
            <input type="file" id="fileInput" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx" style="display: none;">
        </div>

        <!-- Tipos de archivos permitidos -->
        <div class="alert alert-info">
            <div class="d-flex align-items-center">
                <i class="ri-information-line me-2"></i>
                <div>
                    <strong>Tipos de archivos permitidos:</strong>
                    <span class="badge bg-primary ms-2">PDF</span>
                    <span class="badge bg-success ms-1">JPG</span>
                    <span class="badge bg-info ms-1">PNG</span>
                    <span class="badge bg-warning ms-1">DOC</span>
                    <span class="badge bg-danger ms-1">DOCX</span>
                    <span class="badge bg-secondary ms-1">XLS</span>
                    <span class="badge bg-dark ms-1">XLSX</span>
                    <span class="badge bg-light text-dark ms-1">PPT</span>
                    <span class="badge bg-light text-dark ms-1">PPTX</span>
                </div>
            </div>
        </div>

        <!-- Lista de documentos cargados -->
        <div class="documents-list">
            <h6 class="mb-3">
                <i class="ri-folder-line me-2"></i>Documentos Cargados
                <span class="badge bg-primary ms-2" id="documentCount">0</span>
            </h6>
            
            <div id="documentsList" class="documents-list-container">
                <!-- Los documentos se mostrarán aquí dinámicamente -->
            </div>
            
            <!-- Botón para actualizar lista de documentos -->
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-info" onclick="loadExistingDocuments()">
                    <i class="ri-refresh-line me-1"></i>Actualizar Lista
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista previa -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="ri-eye-line me-2"></i>Vista Previa del Documento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent" class="text-center">
                    <!-- El contenido de vista previa se cargará aquí -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="downloadBtn">
                    <i class="ri-download-line me-1"></i>Descargar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar nombre de archivo -->
<div class="modal fade" id="editNameModal" tabindex="-1" aria-labelledby="editNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editNameModalLabel">
                    <i class="ri-edit-line me-2"></i>Editar Nombre del Archivo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editNameForm">
                    <div class="mb-3">
                        <label for="newFileName" class="form-label">Nuevo nombre del archivo:</label>
                        <input type="text" class="form-control" id="newFileName" required>
                        <div class="form-text">Ingresa el nuevo nombre sin la extensión del archivo.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveNameBtn">
                    <i class="ri-save-line me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación personalizado -->
<div class="modal fade" id="deleteDocumentModal" tabindex="-1" aria-labelledby="deleteDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Header rojo -->
            <div class="modal-header bg-danger text-white">
                <div class="d-flex align-items-center">
                    <i class="ri-delete-bin-line me-2"></i>
                    <div>
                        <h5 class="modal-title mb-0" id="deleteDocumentModalLabel">Confirmar Eliminación</h5>
                        <small class="text-white-50">Esta acción no se puede deshacer</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Body blanco -->
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="ri-delete-bin-line text-muted" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">¿Está seguro de eliminar?</h5>
                <p class="text-muted mb-3">
                    <span id="deleteDocumentName">"Documento"</span> será eliminado permanentemente.
                </p>
                <p class="text-muted mb-4">Esta acción no se puede deshacer.</p>
                
                <!-- Advertencia -->
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="ri-alert-line me-2"></i>
                    <div>
                        <strong>Advertencia:</strong> Todos los datos relacionados se perderán permanentemente.
                    </div>
                </div>
            </div>
            
            <!-- Footer con botones -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="ri-delete-bin-line me-1"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para la zona de carga */
    .dropzone {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background: #f8f9fa;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dropzone:hover {
        border-color: #0d6efd;
        background: #f0f8ff;
    }

    .dropzone.dz-drag-hover {
        border-color: #0d6efd;
        background: #e3f2fd;
    }

    .dz-message {
        margin: 0;
        width: 100%;
    }

    /* Ocultar miniaturas de Dropzone para evitar que sobresalgan */
    .dropzone .dz-preview {
        display: none !important;
    }

    /* Asegurar que el área de upload mantenga su tamaño */
    .dropzone.dz-started .dz-message {
        display: none;
    }

    .dropzone.dz-started {
        min-height: 60px;
        padding: 1rem;
    }

    /* Asegurar que el botón de seleccionar archivos sea visible */
    #selectFilesBtn {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Asegurar que el mensaje del dropzone sea visible */
    .dz-message {
        display: block !important;
        visibility: visible !important;
    }

    /* Estilos para las tarjetas de documentos - Diseño compacto */
    .document-card {
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 0.75rem;
        background: #fff;
        transition: all 0.2s ease;
        position: relative;
        margin-bottom: 0.5rem;
    }

    .document-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-color: #0d6efd;
    }

    .document-icon {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
        flex-shrink: 0;
    }

    .document-icon.pdf { background: #dc3545; }
    .document-icon.jpg, .document-icon.png { background: #28a745; }
    .document-icon.doc, .document-icon.docx { background: #007bff; }
    .document-icon.xls, .document-icon.xlsx { background: #17a2b8; }
    .document-icon.ppt, .document-icon.pptx { background: #ffc107; color: #000; }

    .document-actions {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 10;
    }

    .document-card:hover .document-actions {
        opacity: 1;
    }

    /* Asegurar que los botones de acción estén dentro del card */
    .document-card .d-flex.gap-2 {
        gap: 0.5rem !important;
        align-items: center;
        flex-wrap: nowrap;
    }

    .document-card .btn {
        position: relative;
        z-index: 5;
        min-width: 32px;
        min-height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Asegurar que el contenido del card no se desborde */
    .document-card {
        overflow: hidden;
    }

    .document-card .flex-grow-1 {
        min-width: 0;
        flex: 1;
    }

    /* Layout horizontal compacto */
    .document-card .d-flex {
        align-items: center;
        gap: 0.75rem;
    }

    .document-card h6 {
        font-size: 0.875rem;
        font-weight: 600;
        margin: 0;
        color: #495057;
        line-height: 1.2;
    }

    .document-card .text-muted {
        font-size: 0.75rem;
        margin: 0;
    }

    .document-card .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .document-card .btn-sm {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }

    /* Contenedor de documentos compacto */
    .documents-list-container {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    .documents-list-container::-webkit-scrollbar {
        width: 4px;
    }

    .documents-list-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }

    .documents-list-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
    }

    .documents-list-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

.document-preview {
    max-width: 100%;
    max-height: 400px;
    border-radius: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .dropzone {
        padding: 1rem;
    }
    
    .document-card {
        padding: 0.75rem;
    }
}
</style>

<script>
// Función para cargar documentos existentes (global)
window.loadExistingDocuments = function() {
    console.log('=== CARGANDO DOCUMENTOS EXISTENTES ===');
    console.log('URL:', "{{ route('miembros.documentation.index', $miembro->id) }}");
    
    fetch("{{ route('miembros.documentation.index', $miembro->id) }}", {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Respuesta del servidor:', response.status, response.statusText);
        return response.json();
    })
    .then(documents => {
        console.log('Documentos recibidos del servidor:', documents);
        console.log('Cantidad de documentos:', documents.length);
        
        if (documents.length === 0) {
            console.log('No hay documentos para mostrar');
            return;
        }
        
        documents.forEach((doc, index) => {
            console.log(`Procesando documento ${index + 1}:`, doc);
            
            // Crear un objeto file simulado para compatibilidad
            const mockFile = {
                name: doc.name,
                size: doc.size,
                type: doc.mime_type
            };
            
            // Crear response simulado
            const mockResponse = {
                success: true,
                file: {
                    path: doc.path,
                    name: doc.name
                }
            };
            
            console.log('Llamando addDocumentCard con:', mockFile, mockResponse, doc.name);
            addDocumentCard(mockFile, mockResponse, doc.name);
            console.log('Documento agregado a la lista:', doc.name);
        });
        
        console.log('Actualizando contador de documentos...');
        updateDocumentCount();
    })
    .catch(error => {
        console.error('Error al cargar documentos existentes:', error);
    });
};

// Función para agregar tarjeta de documento (global)
window.addDocumentCard = function(file, response, savedFileName = null) {
    console.log('=== addDocumentCard llamada ===');
    console.log('file:', file);
    console.log('response:', response);
    console.log('savedFileName:', savedFileName);
    
    const container = document.getElementById('documentsList');
    console.log('Container encontrado:', container);
    
    if (!container) {
        console.error('Container de documentos no encontrado');
        return;
    }
    
    const fileExtension = file.name.split('.').pop().toLowerCase();
    const iconClass = getIconClass(fileExtension);
    const iconColor = getIconColor(fileExtension);
    
    // Usar el nombre real del archivo guardado si está disponible
    const realFileName = savedFileName || file.name;
    console.log('Nombre del archivo para mostrar:', file.name);
    console.log('Nombre real del archivo guardado:', realFileName);
    
    const documentCard = document.createElement('div');
    documentCard.className = 'document-card';
    documentCard.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="document-icon ${iconClass}" style="background: ${iconColor}">
                <i class="${getFileIcon(fileExtension)}"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="text-truncate" title="${file.name}">${file.name}</h6>
                <p class="text-muted">${formatFileSize(file.size)}</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button class="btn btn-sm btn-outline-primary" onclick="previewDocument('${realFileName}', '${fileExtension}')" title="Vista Previa">
                    <i class="ri-eye-line"></i>
                </button>
                <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('${realFileName}')" title="Descargar">
                    <i class="ri-download-line"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning" onclick="editDocumentName('${file.name}', '${realFileName}')" title="Editar Nombre">
                    <i class="ri-edit-line"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="removeDocument('${file.name}')" title="Eliminar">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
    `;
    
    console.log('Tarjeta de documento creada:', documentCard);
    container.appendChild(documentCard);
    console.log('Tarjeta agregada al container');
    console.log('Container ahora tiene', container.children.length, 'elementos');
};

// Función para actualizar contador de documentos (global)
window.updateDocumentCount = function() {
    const count = document.querySelectorAll('.document-card').length;
    console.log('Actualizando contador de documentos:', count);
    const countElement = document.getElementById('documentCount');
    if (countElement) {
        countElement.textContent = count;
        console.log('Contador actualizado a:', count);
    } else {
        console.error('Elemento de contador no encontrado');
    }
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Iniciando componente de documentación...');
    
    // Cargar documentos existentes al inicializar
    console.log('Inicializando carga de documentos...');
    loadExistingDocuments();
    
    // Verificar si Dropzone está disponible
    if (typeof Dropzone === 'undefined') {
        console.error('Dropzone no está disponible. Usando método alternativo.');
        initAlternativeUpload();
        return;
    }
    
    // Configuración de Dropzone
    Dropzone.autoDiscover = false;
    
    // Configurar input file como respaldo (evitar múltiples listeners)
    const fileInput = document.getElementById('fileInput');
    const selectFilesBtn = document.getElementById('selectFilesBtn');
    
    // Limpiar listeners existentes para evitar duplicados
    if (selectFilesBtn) {
        selectFilesBtn.removeEventListener('click', handleSelectFiles);
        selectFilesBtn.addEventListener('click', handleSelectFiles);
    }
    
    if (fileInput) {
        fileInput.removeEventListener('change', handleFileInput);
        fileInput.addEventListener('change', handleFileInput);
        fileInput.removeEventListener('click', handleFileInputClick);
        fileInput.addEventListener('click', handleFileInputClick);
    }
    
    window.dropzoneInstance = new Dropzone("#documentationDropzone", {
        url: "{{ route('miembros.documentation.upload', $miembro->id) }}",
        paramName: "file",
        maxFilesize: 10, // 10MB
        acceptedFiles: ".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx",
        addRemoveLinks: false, // Desactivar botones de eliminar en miniaturas
        clickable: "#selectFilesBtn",
        previewTemplate: '<div style="display: none;"></div>', // Ocultar miniaturas
        dictDefaultMessage: "",
        dictRemoveFile: "Eliminar",
        dictCancelUpload: "Cancelar",
        dictUploadCanceled: "Carga cancelada",
        dictInvalidFileType: "Tipo de archivo no permitido",
        dictFileTooBig: "El archivo es demasiado grande ({{ config('dropzone.maxFilesize', 10) }}MB max)",
        dictMaxFilesExceeded: "No puedes subir más archivos",
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        init: function() {
            this.on("success", function(file, response) {
                console.log("Archivo cargado exitosamente:", file.name, response);
                console.log("Response completa:", JSON.stringify(response));
                
                // Mostrar toast de éxito usando el sistema global
                showSuccessToast("Archivo guardado automáticamente");
                
                // Limpiar el dropzone después de la carga exitosa
                this.removeAllFiles();
                
                // Restaurar el dropzone
                restoreDropzone();
                
                // Recargar la lista de documentos para mostrar el nuevo archivo
                setTimeout(() => {
                    loadExistingDocuments();
                }, 500); // Pequeño delay para asegurar que el archivo se guardó
            });
            
            this.on("removedfile", function(file) {
                removeDocumentCard(file.name);
                updateDocumentCount();
            });
            
            this.on("error", function(file, errorMessage, xhr) {
                console.error("Error uploading file:", errorMessage, xhr);
                let errorMsg = "Error al cargar el archivo";
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showErrorToast(errorMsg);
            });
            
            this.on("sending", function(file, xhr, formData) {
                console.log("Enviando archivo:", file.name);
                // Mostrar indicador de carga en el área de upload
                const dropzone = document.getElementById('documentationDropzone');
                dropzone.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-2" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mb-0">Subiendo archivo: ${file.name}</p>
                    </div>
                `;
                
                // Timeout de seguridad para evitar carga infinita
                setTimeout(() => {
                    if (dropzone.innerHTML.includes('Subiendo archivo')) {
                        console.warn('Timeout de carga detectado, restaurando dropzone');
                        restoreDropzone();
                        showErrorToast('Error: Tiempo de carga excedido');
                    }
                }, 15000); // 15 segundos timeout
            });
            
            this.on("uploadprogress", function(file, progress, bytesSent) {
                console.log("Progreso de carga:", progress + "%");
            });
            
            this.on("complete", function(file) {
                console.log("Carga completada:", file.name);
                // No restaurar automáticamente para evitar que se abra solo
                // El dropzone se mantendrá en su estado actual
            });
        }
    });

    // Función para agregar tarjeta de documento (global)
    window.addDocumentCard = function(file, response, savedFileName = null) {
        console.log('=== addDocumentCard llamada ===');
        console.log('file:', file);
        console.log('response:', response);
        console.log('savedFileName:', savedFileName);
        
        const container = document.getElementById('documentsList');
        console.log('Container encontrado:', container);
        
        if (!container) {
            console.error('Container de documentos no encontrado');
            return;
        }
        
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const iconClass = getIconClass(fileExtension);
        const iconColor = getIconColor(fileExtension);
        
        // Usar el nombre real del archivo guardado si está disponible
        const realFileName = savedFileName || file.name;
        console.log('Nombre del archivo para mostrar:', file.name);
        console.log('Nombre real del archivo guardado:', realFileName);
        
        const documentCard = document.createElement('div');
        documentCard.className = 'document-card';
        documentCard.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="document-icon ${iconClass}" style="background: ${iconColor}">
                    <i class="${getFileIcon(fileExtension)}"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="text-truncate" title="${file.name}">${file.name}</h6>
                    <p class="text-muted">${formatFileSize(file.size)}</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-sm btn-outline-primary" onclick="previewDocument('${realFileName}', '${fileExtension}')" title="Vista Previa">
                        <i class="ri-eye-line"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('${realFileName}')" title="Descargar">
                        <i class="ri-download-line"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="editDocumentName('${file.name}', '${realFileName}')" title="Editar Nombre">
                        <i class="ri-edit-line"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeDocument('${file.name}')" title="Eliminar">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
        `;
        
        console.log('Tarjeta de documento creada:', documentCard);
        container.appendChild(documentCard);
        console.log('Tarjeta agregada al container');
        console.log('Container ahora tiene', container.children.length, 'elementos');
    }

    // Función para remover tarjeta de documento
    function removeDocumentCard(fileName) {
        const cards = document.querySelectorAll('.document-card');
        cards.forEach(card => {
            const title = card.querySelector('h6').textContent;
            if (title === fileName) {
                card.remove();
            }
        });
    }

    // Función para actualizar contador de documentos (global)
    window.updateDocumentCount = function() {
        const count = document.querySelectorAll('.document-card').length;
        console.log('Actualizando contador de documentos:', count);
        const countElement = document.getElementById('documentCount');
        if (countElement) {
            countElement.textContent = count;
            console.log('Contador actualizado a:', count);
        } else {
            console.error('Elemento de contador no encontrado');
        }
    }

    // Función para obtener clase de icono
    function getIconClass(extension) {
        const iconMap = {
            'pdf': 'pdf',
            'jpg': 'jpg', 'jpeg': 'jpg',
            'png': 'png',
            'doc': 'doc', 'docx': 'doc',
            'xls': 'xls', 'xlsx': 'xls',
            'ppt': 'ppt', 'pptx': 'ppt'
        };
        return iconMap[extension] || 'pdf';
    }

    // Función para obtener color de icono
    function getIconColor(extension) {
        const colorMap = {
            'pdf': '#dc3545',
            'jpg': '#28a745', 'jpeg': '#28a745',
            'png': '#28a745',
            'doc': '#007bff', 'docx': '#007bff',
            'xls': '#17a2b8', 'xlsx': '#17a2b8',
            'ppt': '#ffc107', 'pptx': '#ffc107'
        };
        return colorMap[extension] || '#6c757d';
    }

    // Función para obtener icono de archivo
    function getFileIcon(extension) {
        const iconMap = {
            'pdf': 'ri-file-pdf-line',
            'jpg': 'ri-image-line', 'jpeg': 'ri-image-line',
            'png': 'ri-image-line',
            'doc': 'ri-file-word-line', 'docx': 'ri-file-word-line',
            'xls': 'ri-file-excel-line', 'xlsx': 'ri-file-excel-line',
            'ppt': 'ri-file-ppt-line', 'pptx': 'ri-file-ppt-line'
        };
        return iconMap[extension] || 'ri-file-line';
    }

    // Función para formatear tamaño de archivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Las funciones de toast ahora usan el sistema global

    // Funciones para manejar eventos de archivos (evitar duplicados)
    let isSelectingFiles = false;
    
    function handleSelectFiles(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (isSelectingFiles) {
            console.log('Ya se está seleccionando archivos, ignorando clic');
            return;
        }
        
        isSelectingFiles = true;
        console.log('Botón de seleccionar archivos clickeado');
        
        const fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.click();
        }
        
        // Resetear flag después de un breve delay
        setTimeout(() => {
            isSelectingFiles = false;
        }, 1000);
    }

    function handleFileInput(e) {
        e.stopPropagation();
        console.log('Archivos seleccionados manualmente:', this.files);
        if (this.files.length > 0) {
            const files = Array.from(this.files);
            files.forEach(file => {
                console.log('Agregando archivo a dropzone:', file.name);
                // Intentar usar dropzone si está disponible
                if (window.dropzoneInstance) {
                    window.dropzoneInstance.addFile(file);
                } else {
                    // Fallback al método alternativo
                    uploadFileAlternative(file);
                }
            });
        }
        // Limpiar el input para permitir seleccionar el mismo archivo nuevamente
        this.value = '';
    }

    function handleFileInputClick(e) {
        e.stopPropagation();
        console.log('Input file clickeado');
    }

    // Función para restaurar el dropzone
    function restoreDropzone() {
        const dropzone = document.getElementById('documentationDropzone');
        dropzone.innerHTML = `
            <div class="dz-message">
                <div class="text-center">
                    <i class="ri-upload-cloud-2-line fs-48 text-muted mb-3"></i>
                    <h6 class="mb-2">Arrastra y suelta archivos aquí</h6>
                    <p class="text-muted mb-3">o haz clic para seleccionar archivos</p>
                    <button type="button" class="btn btn-primary btn-sm" id="selectFilesBtn">
                        <i class="ri-add-line me-1"></i>Seleccionar Archivos
                    </button>
                </div>
            </div>
        `;
        
        // Reconfigurar el botón (evitar múltiples listeners)
        const selectFilesBtn = document.getElementById('selectFilesBtn');
        const fileInput = document.getElementById('fileInput');
        if (selectFilesBtn && fileInput) {
            // Remover listeners existentes para evitar duplicados
            selectFilesBtn.removeEventListener('click', handleSelectFiles);
            fileInput.removeEventListener('change', handleFileInput);
            
            // Agregar nuevos listeners
            selectFilesBtn.addEventListener('click', handleSelectFiles);
            fileInput.addEventListener('change', handleFileInput);
        }
    }

    // Función para cargar documentos existentes (global)
    window.loadExistingDocuments = function() {
        console.log('=== CARGANDO DOCUMENTOS EXISTENTES ===');
        console.log('URL:', "{{ route('miembros.documentation.index', $miembro->id) }}");
        
        fetch("{{ route('miembros.documentation.index', $miembro->id) }}", {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Respuesta del servidor:', response.status, response.statusText);
            return response.json();
        })
        .then(documents => {
            console.log('Documentos recibidos del servidor:', documents);
            console.log('Cantidad de documentos:', documents.length);
            
            if (documents.length === 0) {
                console.log('No hay documentos para mostrar');
                return;
            }
            
            // Limpiar documentos existentes antes de cargar nuevos
            const container = document.getElementById('documentsList');
            if (container) {
                container.innerHTML = '';
                console.log('Container limpiado');
            }
            
            documents.forEach((doc, index) => {
                console.log(`Procesando documento ${index + 1}:`, doc);
                
                // Crear un objeto file simulado para compatibilidad
                const mockFile = {
                    name: doc.name,
                    size: doc.size,
                    type: doc.mime_type
                };
                
                // Crear response simulado
                const mockResponse = {
                    success: true,
                    file: {
                        path: doc.path,
                        name: doc.name
                    }
                };
                
                // Usar el nombre real del archivo (con timestamp) para las URLs
                const realFileName = doc.real_name || doc.path.split('/').pop();
                console.log('Nombre real del archivo:', realFileName);
                console.log('Llamando addDocumentCard con:', mockFile, mockResponse, realFileName);
                addDocumentCard(mockFile, mockResponse, realFileName);
                console.log('Documento agregado a la lista:', doc.name);
            });
            
            console.log('Actualizando contador de documentos...');
            updateDocumentCount();
        })
        .catch(error => {
            console.error('Error al cargar documentos existentes:', error);
        });
    }

    // Función alternativa cuando Dropzone no está disponible
    function initAlternativeUpload() {
        console.log('Inicializando método alternativo de carga...');
        
        const fileInput = document.getElementById('fileInput');
        const selectFilesBtn = document.getElementById('selectFilesBtn');
        
        selectFilesBtn.addEventListener('click', function() {
            console.log('Botón de seleccionar archivos clickeado (método alternativo)');
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function(e) {
            console.log('Archivos seleccionados (método alternativo):', e.target.files.length);
            const files = Array.from(e.target.files);
            files.forEach(file => {
                console.log('Procesando archivo:', file.name);
                uploadFileAlternative(file);
            });
        });
        
        // Hacer la zona de drop clickeable
        const dropzone = document.getElementById('documentationDropzone');
        dropzone.addEventListener('click', function() {
            fileInput.click();
        });
        
        // Agregar drag and drop manual
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '#0d6efd';
            dropzone.style.background = '#e3f2fd';
        });
        
        dropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '#dee2e6';
            dropzone.style.background = '#f8f9fa';
        });
        
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.style.borderColor = '#dee2e6';
            dropzone.style.background = '#f8f9fa';
            
            const files = Array.from(e.dataTransfer.files);
            files.forEach(file => {
                console.log('Archivo soltado:', file.name);
                uploadFileAlternative(file);
            });
        });
    }
    
    // Función para subir archivo sin Dropzone
    function uploadFileAlternative(file) {
        console.log('Subiendo archivo alternativo:', file.name);
        
        // Validar tipo de archivo
        const allowedTypes = ['.pdf', '.jpg', '.jpeg', '.png', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!allowedTypes.includes(fileExtension)) {
            showErrorToast('Tipo de archivo no permitido: ' + fileExtension);
            return;
        }
        
        // Validar tamaño (10MB)
        if (file.size > 10 * 1024 * 1024) {
            showErrorToast('El archivo es demasiado grande (máximo 10MB)');
            return;
        }
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Mostrar indicador de carga
        showInfoToast('Subiendo archivo: ' + file.name);
        
        // Timeout de seguridad
        const timeoutId = setTimeout(() => {
            showErrorToast('Error: Tiempo de carga excedido');
        }, 15000);
        
        fetch("{{ route('miembros.documentation.upload', $miembro->id) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            clearTimeout(timeoutId); // Limpiar timeout
            console.log('Respuesta del servidor:', data);
            if (data.success) {
                showSuccessToast('Archivo guardado automáticamente');
                
                // Recargar la lista de documentos para mostrar el nuevo archivo
                setTimeout(() => {
                    loadExistingDocuments();
                }, 500);
            } else {
                showErrorToast(data.message || 'Error al cargar el archivo');
            }
        })
        .catch(error => {
            clearTimeout(timeoutId); // Limpiar timeout
            console.error('Error al subir archivo:', error);
                showErrorToast('Error al cargar el archivo');
        });
    }

    // Funciones globales para los botones
    window.previewDocument = function(fileName, extension) {
        console.log('Abriendo vista previa para:', fileName, extension);
        
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        const previewContent = document.getElementById('previewContent');
        
        // Mostrar loading
        previewContent.innerHTML = `
            <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="ms-3">
                    <p class="mb-0">Cargando vista previa...</p>
                </div>
            </div>
        `;
        
        modal.show();
        
        // Construir URL correcta del archivo - usar el nombre real del archivo guardado
        const fileUrl = `/storage/documents/miembros/{{ $miembro->id }}/${fileName}`;
        console.log('URL del archivo:', fileUrl);
        console.log('Nombre del archivo:', fileName);
        
        // Cargar vista previa según el tipo de archivo
        if (['jpg', 'jpeg', 'png'].includes(extension)) {
            const img = new Image();
            img.onload = function() {
                previewContent.innerHTML = `
                    <div class="text-center">
                        <img src="${fileUrl}" class="document-preview" alt="Vista previa de ${fileName}" style="max-width: 100%; max-height: 500px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    </div>
                `;
            };
            img.onerror = function() {
                previewContent.innerHTML = `
                    <div class="text-center">
                        <i class="ri-image-line fs-48 text-muted mb-3"></i>
                        <h6>No se pudo cargar la imagen</h6>
                        <p class="text-muted">El archivo no se encuentra o no es accesible</p>
                        <button class="btn btn-primary" onclick="downloadDocument('${fileName}')">
                            <i class="ri-download-line me-1"></i>Descargar Archivo
                        </button>
                    </div>
                `;
            };
            img.src = fileUrl;
        } else if (extension === 'pdf') {
            previewContent.innerHTML = `
                <div class="text-center mb-3">
                    <i class="ri-file-pdf-line fs-48 text-danger mb-2"></i>
                    <h6>Vista previa de PDF</h6>
                </div>
                <iframe src="${fileUrl}#toolbar=0&navpanes=0&scrollbar=1" 
                        width="100%" 
                        height="500px" 
                        style="border: 1px solid #dee2e6; border-radius: 8px;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                </iframe>
                <div style="display: none;" class="text-center mt-3">
                    <p class="text-muted">No se pudo cargar el PDF</p>
                    <button class="btn btn-primary" onclick="downloadDocument('${fileName}')">
                        <i class="ri-download-line me-1"></i>Descargar PDF
                    </button>
                </div>
            `;
        } else {
            previewContent.innerHTML = `
                <div class="text-center">
                    <i class="ri-file-line fs-48 text-muted mb-3"></i>
                    <h6>Vista previa no disponible</h6>
                    <p class="text-muted">Este tipo de archivo (${extension.toUpperCase()}) no se puede previsualizar</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-primary" onclick="downloadDocument('${fileName}')">
                            <i class="ri-download-line me-1"></i>Descargar Archivo
                        </button>
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            `;
        }
    };

    window.downloadDocument = function(fileName) {
        console.log('Descargando archivo:', fileName);
        const link = document.createElement('a');
        link.href = `/storage/documents/miembros/{{ $miembro->id }}/${fileName}`;
        link.download = fileName;
        link.click();
    };

    // Variable global para almacenar el nombre del archivo a eliminar
    let documentToDelete = null;

    window.removeDocument = function(fileName) {
        console.log('Solicitando eliminación de documento:', fileName);
        
        // Almacenar el nombre del archivo a eliminar
        documentToDelete = fileName;
        
        // Actualizar el nombre en el modal
        document.getElementById('deleteDocumentName').textContent = `"${fileName}"`;
        
        // Mostrar el modal personalizado
        const modal = new bootstrap.Modal(document.getElementById('deleteDocumentModal'));
        modal.show();
    };

    // Configurar el botón de confirmación de eliminación
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (documentToDelete) {
            console.log('Eliminando documento:', documentToDelete);
            
            // Enviar petición al servidor para eliminar el archivo
            fetch(`{{ route('miembros.documentation.delete', [$miembro->id, 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', encodeURIComponent(documentToDelete)), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    removeDocumentCard(documentToDelete);
                    updateDocumentCount();
                    showSuccessToast('Documento eliminado correctamente');
                    
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteDocumentModal'));
                    modal.hide();
                } else {
                    showErrorToast(data.message || 'Error al eliminar el documento');
                }
            })
            .catch(error => {
                console.error('Error al eliminar documento:', error);
                showErrorToast('Error al eliminar el documento');
            })
            .finally(() => {
                // Limpiar la variable
                documentToDelete = null;
            });
        }
    });

    // Variables globales para edición de nombres
    let currentEditingFile = null;
    let currentRealFileName = null;

    // Función para editar nombre de archivo
    window.editDocumentName = function(displayName, realFileName) {
        console.log('Editando nombre de archivo:', displayName, realFileName);
        
        currentEditingFile = displayName;
        currentRealFileName = realFileName;
        
        console.log('Archivo real para renombrar:', currentRealFileName);
        
        // Extraer nombre sin extensión
        const nameWithoutExtension = displayName.replace(/\.[^/.]+$/, "");
        
        // Configurar modal
        document.getElementById('newFileName').value = nameWithoutExtension;
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('editNameModal'));
        modal.show();
    };

    // Configurar botón de guardar cambios
    document.getElementById('saveNameBtn').addEventListener('click', function() {
        const newName = document.getElementById('newFileName').value.trim();
        
        if (!newName) {
            showErrorToast('El nombre no puede estar vacío');
            return;
        }
        
        if (newName === currentEditingFile.replace(/\.[^/.]+$/, "")) {
            showInfoToast('El nombre no ha cambiado');
            return;
        }
        
        // Obtener extensión del archivo original
        const extension = currentEditingFile.split('.').pop();
        const newFullName = `${newName}.${extension}`;
        
        console.log('Guardando nuevo nombre:', newFullName);
        
        // Enviar petición al servidor para renombrar el archivo
        const renameUrl = `{{ route('miembros.documentation.rename', [$miembro->id, 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', encodeURIComponent(currentRealFileName));
        console.log('URL de renombrar:', renameUrl);
        
        fetch(renameUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                new_name: newName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDocumentName(currentEditingFile, newFullName);
                showSuccessToast('Nombre del archivo actualizado');
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editNameModal'));
                modal.hide();
            } else {
                showErrorToast(data.message || 'Error al renombrar el archivo');
            }
        })
        .catch(error => {
            console.error('Error al renombrar archivo:', error);
            showErrorToast('Error al renombrar el archivo');
        });
    });

    // Función para actualizar el nombre en la interfaz
    function updateDocumentName(oldName, newName) {
        const cards = document.querySelectorAll('.document-card');
        cards.forEach(card => {
            const titleElement = card.querySelector('h6');
            if (titleElement && titleElement.textContent === oldName) {
                titleElement.textContent = newName;
                titleElement.title = newName;
                
                // Actualizar los onclick con el nuevo nombre
                const buttons = card.querySelectorAll('button');
                buttons.forEach(button => {
                    const onclick = button.getAttribute('onclick');
                    if (onclick && onclick.includes(oldName)) {
                        button.setAttribute('onclick', onclick.replace(oldName, newName));
                    }
                });
            }
        });
    }
});
</script>
