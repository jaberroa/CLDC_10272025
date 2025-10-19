@extends('partials.layouts.master')

@section('title', 'Cerrar Sesión | CLDCI - Sistema de Gestión')

@section('content')
<div class="auth-page-wrapper pt-5">
    <!-- auth page bg -->
    <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
        <div class="bg-overlay"></div>
        
        <div class="shape">
            <svg viewBox="0 0 1440 120" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <defs>
                    <linearGradient id="tpshape" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color: #6366f1;stop-opacity:1" />
                        <stop offset="100%" style="stop-color: #8b5cf6;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <path d="M0,0 C480,120 960,0 1440,120 L1440,120 L0,120 Z" fill="url(#tpshape)"></path>
            </svg>
        </div>
    </div>

    <!-- auth page content -->
    <div class="auth-page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mt-sm-5 mb-4 text-white-50">
                        <div>
                            <a href="{{ route('dashboard') }}" class="d-inline-block auth-logo">
                                <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="20">
                            </a>
                        </div>
                        <p class="mt-3 fs-15 fw-medium">Sistema de Gestión CLDCI</p>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <div class="avatar-md mx-auto mb-4">
                                    <div class="avatar-title rounded-circle bg-light text-warning display-4">
                                        <i class="ri-logout-box-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-primary">¿Cerrar Sesión?</h5>
                                <p class="text-muted">¿Estás seguro de que deseas cerrar tu sesión en CLDCI?</p>
                            </div>
                            
                            <div class="p-2 mt-4">
                                <!-- Información del usuario -->
                                <div class="mb-4">
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="ri-user-line me-2"></i>
                                        <div>
                                            <strong>Sesión activa:</strong><br>
                                            <span id="user-info">Usuario: {{ auth()->user()->email ?? 'usuario@ejemplo.com' }}</span><br>
                                            <small class="text-muted">Último acceso: {{ auth()->user()->last_login_at ?? 'hace 5 minutos' }}</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="row g-2">
                                    <div class="col-6">
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button class="btn btn-danger w-100" type="submit">
                                                <i class="ri-logout-box-line me-1"></i>
                                                Sí, Cerrar Sesión
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary w-100">
                                            <i class="ri-arrow-left-line me-1"></i>
                                            Cancelar
                                        </a>
                                    </div>
                                </div>

                                <!-- Información adicional -->
                                <div class="mt-4">
                                    <div class="alert alert-light" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-information-line text-info me-2 mt-1"></i>
                                            <div>
                                                <h6 class="alert-heading mb-1">¿Qué pasa al cerrar sesión?</h6>
                                                <p class="mb-0 small">
                                                    Se cerrará tu sesión actual y deberás iniciar sesión nuevamente 
                                                    para acceder al sistema.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opciones adicionales -->
                    <div class="mt-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <h6 class="text-center mb-3 text-muted">Otras Opciones</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="/perfil" class="btn btn-outline-info btn-sm w-100 d-flex flex-column align-items-center py-2">
                                            <i class="ri-user-settings-line fs-4 mb-1"></i>
                                            <small>Mi Perfil</small>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('dashboard') }}" class="btn btn-outline-success btn-sm w-100 d-flex flex-column align-items-center py-2">
                                            <i class="ri-dashboard-3-line fs-4 mb-1"></i>
                                            <small>Dashboard</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de seguridad -->
                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="text-center mb-3 text-muted">Información de Seguridad</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ri-shield-check-line fs-3 text-success mb-2"></i>
                                            <small class="text-muted">Sesión Segura</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ri-time-line fs-3 text-info mb-2"></i>
                                            <small class="text-muted">Auto-logout</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ri-lock-line fs-3 text-primary mb-2"></i>
                                            <small class="text-muted">Datos Protegidos</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enlaces adicionales -->
                    <div class="mt-4 text-center">
                        <p class="mb-0">
                            ¿Necesitas ayuda? 
                            <a href="mailto:soporte@cldci.org.do" class="fw-semibold text-primary text-decoration-underline">Contacta soporte</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="mb-0 text-muted">&copy; {{ date('Y') }} CLDCI. Todos los derechos reservados.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection

@section('js')
<script>
// Auto-logout después de 5 minutos de inactividad
let inactivityTimer;
const INACTIVITY_TIMEOUT = 5 * 60 * 1000; // 5 minutos

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(function() {
        // Mostrar confirmación antes del auto-logout
        if (confirm('Tu sesión expirará por inactividad. ¿Deseas continuar?')) {
            resetInactivityTimer();
        } else {
            // Cerrar sesión automáticamente
            document.querySelector('form[action="{{ route('logout') }}"]').submit();
        }
    }, INACTIVITY_TIMEOUT);
}

// Eventos que resetean el timer de inactividad
const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
events.forEach(function(event) {
    document.addEventListener(event, resetInactivityTimer, true);
});

// Iniciar timer cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    resetInactivityTimer();
});

// Limpiar timer cuando se sale de la página
window.addEventListener('beforeunload', function() {
    clearTimeout(inactivityTimer);
});

// Confirmación antes de cerrar sesión
document.querySelector('form[action="{{ route('logout') }}"]').addEventListener('submit', function(e) {
    if (!confirm('¿Estás seguro de que deseas cerrar tu sesión?')) {
        e.preventDefault();
    }
});
</script>
@endsection
