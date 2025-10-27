<div class="card-header elecciones-index-header">
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0 me-3">
            <a href="{{ route('elecciones.create') }}" class="btn btn-agregar">
                <i class="ri-add-line"></i>
                <span>Nueva Elección</span>
            </a>
        </div>
        <div class="flex-grow-1">
            <h4 class="card-title">
                <i class="ri-vote-line"></i>
                Todas las Elecciones
            </h4>
            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.9rem; opacity: 0.8;">
                Gestione y administre todas las elecciones registradas en el sistema
            </p>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3">
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown" id="bulkActionsDropdown" style="display: none;">
                <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ri-checkbox-multiple-line me-1"></i> Acciones Masivas
                    <span id="selectedCount" class="badge bg-light text-dark ms-1">0</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="bulkAction('export')">
                        <i class="ri-download-line me-2"></i> Exportar Seleccionadas
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="bulkAction('print')">
                        <i class="ri-printer-line me-2"></i> Imprimir Seleccionadas
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="bulkAction('status')">
                        <i class="ri-toggle-line me-2"></i> Cambiar Estado
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                        <i class="ri-delete-bin-line me-2"></i> Eliminar Seleccionadas
                    </a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-outline-light btn-sm" onclick="exportarElecciones()">
                <i class="ri-download-line me-1"></i> Exportar
            </button>
            <button type="button" class="btn btn-outline-light btn-sm" onclick="imprimirLista()">
                <i class="ri-printer-line me-1"></i> Imprimir
            </button>
        </div>
    </div>
</div>
