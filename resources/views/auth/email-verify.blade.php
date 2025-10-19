@extends('partials.layouts.master')

@section('title', 'Verificar Email | CLDCI - Sistema de Gestión')

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
                                    <div class="avatar-title rounded-circle bg-light text-info display-4">
                                        <i class="ri-mail-check-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-primary">Verifica tu Email</h5>
                                <p class="text-muted">Hemos enviado un enlace de verificación a tu dirección de correo electrónico.</p>
                            </div>
                            
                            <div class="p-2 mt-4">
                                <!-- Email del usuario -->
                                <div class="mb-4">
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="ri-mail-line me-2"></i>
                                        <div>
                                            <strong>Email enviado a:</strong><br>
                                            <span id="user-email">{{ auth()->user()->email ?? 'usuario@ejemplo.com' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botón para reenviar -->
                                <form action="{{ route('verification.send') }}" method="POST">
                                    @csrf
                                    <div class="d-grid">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="ri-refresh-line me-1"></i>
                                            Reenviar Email de Verificación
                                        </button>
                                    </div>
                                </form>

                                <!-- Información adicional -->
                                <div class="mt-4">
                                    <div class="alert alert-light" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="ri-information-line text-info me-2 mt-1"></i>
                                            <div>
                                                <h6 class="alert-heading mb-1">¿No recibiste el email?</h6>
                                                <p class="mb-0 small">
                                                    Revisa tu carpeta de spam o correo no deseado. 
                                                    El enlace de verificación expira en 24 horas.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pasos de verificación -->
                    <div class="mt-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <h6 class="text-center mb-3 text-muted">Proceso de Verificación</h6>
                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center mb-2">
                                                <span class="text-white fw-bold">1</span>
                                            </div>
                                            <small class="text-muted">Revisa tu email</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center mb-2">
                                                <span class="text-white fw-bold">2</span>
                                            </div>
                                            <small class="text-muted">Haz clic en el enlace</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center mb-2">
                                                <span class="text-white fw-bold">3</span>
                                            </div>
                                            <small class="text-muted">Confirma tu cuenta</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center mb-2">
                                                <span class="text-white fw-bold">4</span>
                                            </div>
                                            <small class="text-muted">¡Listo!</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Beneficios de verificación -->
                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="text-center mb-3 text-muted">Beneficios de Verificar tu Email</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ri-shield-check-line fs-3 text-success mb-2"></i>
                                            <small class="text-muted">Seguridad</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ri-notification-3-line fs-3 text-info mb-2"></i>
                                            <small class="text-muted">Notificaciones</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ri-user-settings-line fs-3 text-primary mb-2"></i>
                                            <small class="text-muted">Acceso Completo</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enlaces adicionales -->
                    <div class="mt-4 text-center">
                        <p class="mb-0">
                            ¿Ya verificaste tu email? 
                            <a href="{{ route('dashboard') }}" class="fw-semibold text-primary text-decoration-underline">Ir al Dashboard</a>
                        </p>
                    </div>

                    <!-- Contacto de soporte -->
                    <div class="mt-3 text-center">
                        <p class="text-muted small">
                            ¿Problemas con la verificación? 
                            <a href="mailto:soporte@cldci.org.do" class="text-primary">Contacta soporte</a>
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
// Auto-refresh cada 30 segundos para verificar si el email fue verificado
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        // Verificar si el email ha sido verificado
        fetch('/email/verify/check', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.verified) {
                clearInterval(refreshInterval);
                window.location.href = '/dashboard';
            }
        })
        .catch(error => {
            console.log('Error verificando estado:', error);
        });
    }, 30000); // 30 segundos
}

// Iniciar auto-refresh cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});

// Limpiar intervalo cuando se sale de la página
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>
@endsection

