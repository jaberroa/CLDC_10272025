{{-- Modal de Confirmación de Eliminación --}}
<div class="modal fade delete-confirmation-modal" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            {{-- Header del Modal --}}
            <div class="modal-header bg-danger text-white border-0">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3">
                        <i class="ri-delete-bin-line text-white fs-18"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="deleteConfirmationModalLabel">
                            Confirmar Eliminación
                        </h5>
                        <small class="opacity-75">Esta acción no se puede deshacer</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body del Modal --}}
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="avatar-lg bg-light-danger mx-auto mb-3">
                        <i class="ri-delete-bin-line text-danger fs-24"></i>
                    </div>
                    <h6 class="text-muted mb-2">¿Está seguro de eliminar?</h6>
                    <p class="text-muted mb-0" id="deleteConfirmationText">
                        Esta acción eliminará permanentemente el elemento seleccionado.
                    </p>
                </div>

                {{-- Información adicional --}}
                <div class="alert alert-warning border-0 bg-light-warning" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ri-alert-line text-warning me-2 mt-1"></i>
                        <div>
                            <small class="text-warning-emphasis">
                                <strong>Advertencia:</strong> Todos los datos relacionados se perderán permanentemente.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer del Modal --}}
            <div class="modal-footer border-0 p-4 pt-0">
                <div class="d-flex gap-2 w-100">
                    <button type="button" class="btn btn-light flex-fill" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger flex-fill" id="confirmDeleteBtn">
                        <i class="ri-delete-bin-line me-1"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Estilos personalizados --}}
<style>
.delete-confirmation-modal .modal-content {
    border-radius: 12px;
    overflow: hidden;
}

.delete-confirmation-modal .modal-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.delete-confirmation-modal .avatar-sm {
    width: 2rem;
    height: 2rem;
}

.delete-confirmation-modal .avatar-lg {
    width: 4rem;
    height: 4rem;
}

.delete-confirmation-modal .bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.delete-confirmation-modal .bg-light-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.delete-confirmation-modal .bg-light-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.delete-confirmation-modal .text-warning-emphasis {
    color: #664d03 !important;
}

.delete-confirmation-modal .btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    transition: all 0.3s ease;
}

.delete-confirmation-modal .btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.delete-confirmation-modal .btn-light {
    transition: all 0.3s ease;
}

.delete-confirmation-modal .btn-light:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
}


/* Responsive */
@media (max-width: 576px) {
    .delete-confirmation-modal .modal-dialog {
        margin: 1rem;
    }
    
    .delete-confirmation-modal .modal-footer .d-flex {
        flex-direction: column;
    }
    
    .delete-confirmation-modal .modal-footer .btn {
        margin-bottom: 0.5rem;
    }
}
</style>
