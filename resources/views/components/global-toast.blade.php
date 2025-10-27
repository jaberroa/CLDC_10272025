{{-- Toast Global de Notificaciones con Transición de 3 segundos --}}
<style>
.toast {
    animation: slideInRight 0.3s ease-out;
    transition: opacity 3s ease-in-out, transform 3s ease-in-out;
    display: none; /* Oculto por defecto */
}

.toast.show {
    display: block !important; /* Visible cuando tiene clase show */
}

/* Evitar conflictos con Bootstrap */
.toast.showing {
    opacity: 1 !important;
}

.toast.fade-out {
    opacity: 0 !important;
    transform: translateX(100%) !important;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="globalToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header border-0">
            <div class="avatar-sm bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-2">
                <i class="text-white fs-14" id="toastIcon"></i>
            </div>
            <strong class="me-auto" id="toastTitle">Notificación</strong>
            <small class="opacity-75" id="toastTime">Ahora</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <div class="d-flex align-items-center">
                <i class="me-2 fs-16" id="toastBodyIcon"></i>
                <span id="toastMessage">Mensaje de notificación</span>
            </div>
        </div>
    </div>
</div>

{{-- Estilos del Toast Global --}}
<style>
/* Animaciones del toast con transición de 3 segundos */
.toast {
    animation: slideInRight 0.3s ease-out;
    transition: all 3s ease-in-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.show {
    animation: slideInRight 0.3s ease-out;
}

/* Efecto de desvanecimiento de 3 segundos */
.toast.fade-out {
    animation: fadeOut 3s ease-in-out forwards;
}

@keyframes fadeOut {
    0% {
        opacity: 1;
        transform: translateX(0);
    }
    70% {
        opacity: 1;
        transform: translateX(0);
    }
    100% {
        opacity: 0;
        transform: translateX(100%);
    }
}

/* Estilos para diferentes tipos de toast */
.toast-success .toast-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.toast-success .toast-body {
    background-color: rgba(40, 167, 69, 0.1);
}

.toast-info .toast-header {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    color: white;
}

.toast-info .toast-body {
    background-color: rgba(23, 162, 184, 0.1);
}

.toast-warning .toast-header {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: #212529;
}

.toast-warning .toast-body {
    background-color: rgba(255, 193, 7, 0.1);
}

.toast-error .toast-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.toast-error .toast-body {
    background-color: rgba(220, 53, 69, 0.1);
}

/* Responsive para móviles */
@media (max-width: 576px) {
    .toast-container {
        padding: 0.5rem !important;
        top: 1rem !important;
        right: 0.5rem !important;
        left: 0.5rem !important;
    }
    
    .toast {
        width: 100% !important;
        max-width: none !important;
    }
}
</style>
