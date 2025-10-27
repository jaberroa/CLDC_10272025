/**
 * Sistema de Toast Global Simplificado
 * Compatible con navegadores y sin módulos ES6
 */

// Función para mostrar toast de éxito
function showSuccessToast(message, title = 'Éxito') {
    console.log('showSuccessToast:', message);
    showToast(message, 'success', title);
}

// Función para mostrar toast de información
function showInfoToast(message, title = 'Información') {
    console.log('showInfoToast:', message);
    showToast(message, 'info', title);
}

// Función para mostrar toast de advertencia
function showWarningToast(message, title = 'Advertencia') {
    console.log('showWarningToast:', message);
    showToast(message, 'warning', title);
}

// Función para mostrar toast de error
function showErrorToast(message, title = 'Error') {
    console.log('showErrorToast:', message);
    showToast(message, 'error', title);
}

// Variable global para controlar duplicados
let toastShown = false;

// Función principal para mostrar toast
function showToast(message, type = 'success', title = null) {
    console.log('showToast:', { message, type, title });
    
    // Evitar duplicados
    if (toastShown) {
        console.log('Toast ya mostrado, evitando duplicado');
        return;
    }
    
    toastShown = true;
    
    const toastElement = document.getElementById('globalToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastTitle = document.getElementById('toastTitle');
    const toastIcon = document.getElementById('toastIcon');
    const toastBodyIcon = document.getElementById('toastBodyIcon');
    const toastTime = document.getElementById('toastTime');
    
    if (!toastElement || !toastMessage) {
        console.error('Elementos del toast no encontrados');
        toastShown = false;
        return;
    }
    
    // Configurar mensaje y título
    toastMessage.textContent = message;
    toastTitle.textContent = title || getDefaultTitle(type);
    toastTime.textContent = getCurrentTime();
    
    // Resetear clases del toast
    toastElement.className = 'toast border-0 shadow-lg';
    
    // Configurar según tipo
    const config = getToastConfig(type);
    
    // Aplicar clases y contenido
    toastElement.classList.add(config.toastClass);
    if (toastIcon) toastIcon.className = `text-white fs-14 ${config.headerIcon}`;
    if (toastBodyIcon) toastBodyIcon.className = `me-2 fs-16 ${config.bodyIcon}`;
    
    // Mostrar toast
    displayToast(toastElement);
}

// Función para mostrar toast con Bootstrap
function displayToast(toastElement) {
    console.log('displayToast llamado, elemento:', toastElement);
    
    // Ocultar cualquier toast anterior
    const existingToast = bootstrap.Toast.getInstance(toastElement);
    if (existingToast) {
        existingToast.hide();
    }
    
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        console.log('Usando Bootstrap Toast');
        
        // Crear nueva instancia del toast
        const toast = new bootstrap.Toast(toastElement, {
            autohide: false, // Controlamos manualmente el ocultado
            delay: 0
        });
        
        // Mostrar toast
        toast.show();
        
        // Iniciar efecto de desvanecimiento después de 3 segundos
        setTimeout(() => {
            console.log('Iniciando desvanecimiento...');
            toastElement.classList.add('fade-out');
            
            // Ocultar completamente después de la animación de desvanecimiento
            setTimeout(() => {
                console.log('Ocultando toast...');
                toast.hide();
                toastElement.classList.remove('fade-out');
                // Resetear flag después de completar
                toastShown = false;
            }, 3000); // Duración de la animación de desvanecimiento
        }, 3000); // Tiempo de visualización antes del desvanecimiento
    } else {
        console.log('Usando fallback manual');
        // Fallback manual
        toastElement.style.display = 'block';
        toastElement.classList.add('show');
        
            setTimeout(() => {
                toastElement.classList.add('fade-out');
                setTimeout(() => {
                    toastElement.style.display = 'none';
                    toastElement.classList.remove('show', 'fade-out');
                    // Resetear flag después de completar
                    toastShown = false;
                }, 3000);
            }, 3000);
    }
}

// Función para obtener configuración del toast
function getToastConfig(type) {
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

// Función para obtener título por defecto
function getDefaultTitle(type) {
    return getToastConfig(type).title;
}

// Función para obtener hora actual
function getCurrentTime() {
    const now = new Date();
    return now.toLocaleTimeString('es-ES', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

// Función para mostrar modal de confirmación de eliminación
function showDeleteConfirmation(options) {
    console.log('showDeleteConfirmation:', options);
    
    const modal = document.getElementById('deleteConfirmationModal');
    const modalText = document.getElementById('deleteConfirmationText');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    if (!modal || !modalText || !confirmBtn) {
        console.error('Elementos del modal no encontrados');
        return;
    }
    
    // Configurar texto del modal
    if (options.message) {
        modalText.textContent = options.message;
    } else {
        modalText.innerHTML = `<strong>"${options.title}"</strong> será eliminado permanentemente.<br><small class="text-muted">Esta acción no se puede deshacer.</small>`;
    }
    
    // Configurar acción de confirmación
    confirmBtn.onclick = function() {
        if (options.onConfirm && typeof options.onConfirm === 'function') {
            options.onConfirm();
        }
        hideModal();
    };
    
    // Mostrar modal
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const bsModal = new bootstrap.Modal(modal, {
            backdrop: 'static',
            keyboard: false
        });
        bsModal.show();
    } else {
        modal.style.display = 'block';
        modal.classList.add('show');
    }
}

// Función para ocultar modal
function hideModal() {
    const modal = document.getElementById('deleteConfirmationModal');
    if (modal) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        } else {
            modal.style.display = 'none';
            modal.classList.remove('show');
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema de toast inicializado');
    
    // Asegurar que las funciones estén disponibles globalmente
    window.showSuccessToast = showSuccessToast;
    window.showInfoToast = showInfoToast;
    window.showWarningToast = showWarningToast;
    window.showErrorToast = showErrorToast;
    window.showToast = showToast;
    window.showDeleteConfirmation = showDeleteConfirmation;
    
    console.log('Funciones disponibles:', {
        showSuccessToast: typeof window.showSuccessToast,
        showInfoToast: typeof window.showInfoToast,
        showWarningToast: typeof window.showWarningToast,
        showErrorToast: typeof window.showErrorToast,
        showToast: typeof window.showToast,
        showDeleteConfirmation: typeof window.showDeleteConfirmation
    });
});
