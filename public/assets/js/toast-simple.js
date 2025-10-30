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

    // Configurar iconos según tipo
    const iconClass = getIconClass(type);
    if (toastIcon) toastIcon.className = `ri-24px ${iconClass}`;
    if (toastBodyIcon) toastBodyIcon.className = `ri-18px ${iconClass}`;

    // Actualizar hora
    if (toastTime) toastTime.textContent = new Date().toLocaleTimeString();

    // Mostrar toast (Bootstrap v5)
    const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
    toast.show();

    // Reset bandera cuando se oculta
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastShown = false;
    }, { once: true });
}

function getDefaultTitle(type) {
    switch (type) {
        case 'success': return 'Operación exitosa';
        case 'info': return 'Información';
        case 'warning': return 'Atención';
        case 'error': return 'Error';
        default: return 'Notificación';
    }
}

function getIconClass(type) {
    switch (type) {
        case 'success': return 'ri-checkbox-circle-line text-success';
        case 'info': return 'ri-information-line text-info';
        case 'warning': return 'ri-alert-line text-warning';
        case 'error': return 'ri-close-circle-line text-danger';
        default: return 'ri-notification-3-line text-primary';
    }
}

window.showSuccessToast = showSuccessToast;
window.showInfoToast = showInfoToast;
window.showWarningToast = showWarningToast;
window.showErrorToast = showErrorToast;
window.showToast = showToast;

