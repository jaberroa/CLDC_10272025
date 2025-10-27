/**
 * Sistema de Confirmación de Eliminación y Toast de Éxito
 * Diseño coherente con el proyecto CLDCI
 */

// Verificar que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando DeleteConfirmation...');
});

class DeleteConfirmation {
    constructor() {
        this.modal = null;
        this.confirmBtn = null;
        this.currentAction = null;
        this.setupModal();
    }

    setupModal() {
        this.modal = document.getElementById('deleteConfirmationModal');
        this.confirmBtn = document.getElementById('confirmDeleteBtn');
        
        if (this.modal && this.confirmBtn) {
            this.confirmBtn.addEventListener('click', () => this.executeDelete());
        }
    }

    /**
     * Mostrar modal de confirmación
     * @param {Object} options - Opciones de configuración
     * @param {string} options.title - Título del elemento a eliminar
     * @param {string} options.message - Mensaje personalizado
     * @param {Function} options.onConfirm - Función a ejecutar al confirmar
     * @param {string} options.type - Tipo de elemento (miembro, cuota, etc.)
     */
    show(options = {}) {
        const {
            title = 'este elemento',
            message = null,
            onConfirm = null,
            type = 'elemento'
        } = options;

        // Configurar texto del modal
        const modalText = document.getElementById('deleteConfirmationText');
        if (modalText) {
            if (message) {
                modalText.textContent = message;
            } else {
                modalText.innerHTML = `
                    <strong>"${title}"</strong> será eliminado permanentemente.
                    <br><small class="text-muted">Esta acción no se puede deshacer.</small>
                `;
            }
        }

        // Configurar acción de confirmación
        this.currentAction = onConfirm;

        // Mostrar modal
        if (this.modal) {
            const bsModal = new bootstrap.Modal(this.modal, {
                backdrop: 'static',
                keyboard: false
            });
            bsModal.show();
        }
    }

    /**
     * Ejecutar eliminación confirmada
     */
    executeDelete() {
        if (this.currentAction && typeof this.currentAction === 'function') {
            try {
                this.currentAction();
                this.hide();
            } catch (error) {
                console.error('Error al ejecutar eliminación:', error);
                this.showError('Error al eliminar el elemento');
            }
        }
    }

    /**
     * Ocultar modal
     */
    hide() {
        if (this.modal) {
            const bsModal = bootstrap.Modal.getInstance(this.modal);
            if (bsModal) {
                bsModal.hide();
            }
        }
    }

    /**
     * Mostrar toast global con transición de 3 segundos
     * @param {string} message - Mensaje del toast
     * @param {string} type - Tipo de toast (success, info, warning, error)
     * @param {string} title - Título del toast (opcional)
     */
    showToast(message = 'Operación completada exitosamente', type = 'success', title = null) {
        console.log('showToast llamado:', { message, type, title });
        
        const toastElement = document.getElementById('globalToast');
        const toastMessage = document.getElementById('toastMessage');
        const toastTitle = document.getElementById('toastTitle');
        const toastIcon = document.getElementById('toastIcon');
        const toastBodyIcon = document.getElementById('toastBodyIcon');
        const toastTime = document.getElementById('toastTime');
        
        console.log('Elementos encontrados:', {
            toastElement: !!toastElement,
            toastMessage: !!toastMessage,
            toastTitle: !!toastTitle,
            toastIcon: !!toastIcon,
            toastBodyIcon: !!toastBodyIcon,
            toastTime: !!toastTime
        });
        
        if (toastElement && toastMessage) {
            // Configurar mensaje y título
            toastMessage.textContent = message;
            toastTitle.textContent = title || this.getDefaultTitle(type);
            toastTime.textContent = this.getCurrentTime();
            
            // Resetear clases del toast
            toastElement.className = 'toast border-0 shadow-lg';
            
            // Configurar según tipo
            const config = this.getToastConfig(type);
            console.log('Configuración del toast:', config);
            
            // Aplicar clases y contenido
            toastElement.classList.add(config.toastClass);
            toastIcon.className = `text-white fs-14 ${config.headerIcon}`;
            toastBodyIcon.className = `me-2 fs-16 ${config.bodyIcon}`;
            
            console.log('Toast configurado, mostrando...');
            
            // Mostrar toast con efecto de desvanecimiento
            this.displayToastWithFade(toastElement);
        } else {
            console.error('Elementos del toast no encontrados');
        }
    }

    /**
     * Mostrar toast con efecto de desvanecimiento de 3 segundos
     * @param {HTMLElement} toastElement - Elemento del toast
     */
    displayToastWithFade(toastElement) {
        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap no está disponible');
            this.showToastFallback(toastElement);
            return;
        }
        
        // Ocultar toast anterior si existe
        const existingToast = bootstrap.Toast.getInstance(toastElement);
        if (existingToast) {
            existingToast.hide();
        }
        
        // Crear nueva instancia del toast
        const toast = new bootstrap.Toast(toastElement, {
            autohide: false, // Controlamos manualmente el ocultado
            delay: 0
        });
        
        // Mostrar toast
        toast.show();
        
        // Iniciar efecto de desvanecimiento después de 3 segundos
        setTimeout(() => {
            toastElement.classList.add('fade-out');
            
            // Ocultar completamente después de la animación
            setTimeout(() => {
                toast.hide();
                toastElement.classList.remove('fade-out');
            }, 3000); // Duración de la animación de desvanecimiento
        }, 3000); // Tiempo de visualización antes del desvanecimiento
    }

    /**
     * Fallback para mostrar toast sin Bootstrap
     * @param {HTMLElement} toastElement - Elemento del toast
     */
    showToastFallback(toastElement) {
        // Mostrar toast manualmente
        toastElement.style.display = 'block';
        toastElement.classList.add('show');
        
        // Ocultar después de 6 segundos
        setTimeout(() => {
            toastElement.classList.add('fade-out');
            setTimeout(() => {
                toastElement.style.display = 'none';
                toastElement.classList.remove('show', 'fade-out');
            }, 3000);
        }, 3000);
    }

    /**
     * Obtener configuración del toast según el tipo
     * @param {string} type - Tipo de toast
     * @returns {Object} Configuración del toast
     */
    getToastConfig(type) {
        const configs = {
            success: {
                toastClass: 'toast-success',
                headerIcon: 'ri-check-line',
                bodyIcon: 'ri-check-circle-line text-success',
                title: 'Éxito'
            },
            info: {
                toastClass: 'toast-info',
                headerIcon: 'ri-information-line',
                bodyIcon: 'ri-information-line text-info',
                title: 'Información'
            },
            warning: {
                toastClass: 'toast-warning',
                headerIcon: 'ri-alert-line',
                bodyIcon: 'ri-alert-line text-warning',
                title: 'Advertencia'
            },
            error: {
                toastClass: 'toast-error',
                headerIcon: 'ri-error-warning-line',
                bodyIcon: 'ri-error-warning-line text-danger',
                title: 'Error'
            }
        };
        
        return configs[type] || configs.success;
    }

    /**
     * Obtener título por defecto según el tipo
     * @param {string} type - Tipo de toast
     * @returns {string} Título por defecto
     */
    getDefaultTitle(type) {
        return this.getToastConfig(type).title;
    }

    /**
     * Obtener hora actual formateada
     * @returns {string} Hora actual
     */
    getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    /**
     * Mostrar toast de éxito (método de compatibilidad)
     * @param {string} message - Mensaje de éxito
     * @param {string} type - Tipo de éxito
     */
    showSuccess(message = 'Operación completada exitosamente', type = 'success') {
        this.showToast(message, type, 'Éxito');
    }

    /**
     * Mostrar toast de error (método de compatibilidad)
     * @param {string} message - Mensaje de error
     */
    showError(message = 'Error al procesar la solicitud') {
        this.showToast(message, 'error', 'Error');
    }
}

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando sistema de toast...');
    
    // Crear instancia global
    window.deleteConfirmation = new DeleteConfirmation();
    
    // Funciones de conveniencia para uso global
    window.showDeleteConfirmation = function(options) {
        console.log('showDeleteConfirmation llamado:', options);
        if (window.deleteConfirmation) {
            window.deleteConfirmation.show(options);
        } else {
            console.error('deleteConfirmation no está disponible');
        }
    };

    window.showSuccessToast = function(message, type) {
        console.log('showSuccessToast llamado:', message, type);
        if (window.deleteConfirmation) {
            window.deleteConfirmation.showSuccess(message, type);
        } else {
            console.error('deleteConfirmation no está disponible');
        }
    };

    window.showErrorToast = function(message) {
        console.log('showErrorToast llamado:', message);
        if (window.deleteConfirmation) {
            window.deleteConfirmation.showError(message);
        } else {
            console.error('deleteConfirmation no está disponible');
        }
    };

    // Función global para mostrar toast con transición de 3 segundos
    window.showToast = function(message, type = 'success', title = null) {
        console.log('showToast llamado:', message, type, title);
        if (window.deleteConfirmation) {
            window.deleteConfirmation.showToast(message, type, title);
        } else {
            console.error('deleteConfirmation no está disponible');
        }
    };

    // Funciones específicas por tipo
    window.showInfoToast = function(message, title = 'Información') {
        console.log('showInfoToast llamado:', message, title);
        if (window.deleteConfirmation) {
            window.deleteConfirmation.showToast(message, 'info', title);
        } else {
            console.error('deleteConfirmation no está disponible');
        }
    };

    window.showWarningToast = function(message, title = 'Advertencia') {
        console.log('showWarningToast llamado:', message, title);
        if (window.deleteConfirmation) {
            window.deleteConfirmation.showToast(message, 'warning', title);
        } else {
            console.error('deleteConfirmation no está disponible');
        }
    };
    
    console.log('Funciones del toast inicializadas:', {
        showDeleteConfirmation: typeof window.showDeleteConfirmation,
        showSuccessToast: typeof window.showSuccessToast,
        showErrorToast: typeof window.showErrorToast,
        showToast: typeof window.showToast,
        showInfoToast: typeof window.showInfoToast,
        showWarningToast: typeof window.showWarningToast,
        deleteConfirmation: !!window.deleteConfirmation
    });
});

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DeleteConfirmation;
}
