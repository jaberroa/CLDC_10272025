@extends('partials.layouts.master')

@section('title', 'Recuperar Contraseña | CLDCI - Sistema de Gestión')

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
                                    <div class="avatar-title rounded-circle bg-light text-primary display-4">
                                        <i class="ri-mail-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-primary">¿Olvidaste tu contraseña?</h5>
                                <p class="text-muted">No te preocupes, te enviaremos un enlace para restablecer tu contraseña.</p>
                            </div>
                            
                            <div class="p-2 mt-4">
                                <form action="{{ route('password.request') }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" placeholder="Ingresa tu email registrado" 
                                               value="{{ old('email') }}" required autofocus>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mt-4">
                                        <button class="btn btn-primary w-100" type="submit">
                                            <i class="ri-mail-send-line me-1"></i>
                                            Enviar Enlace de Recuperación
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-soft-info text-info rounded-circle fs-4">
                                                <i class="ri-information-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">¿No recibiste el email?</h6>
                                        <p class="text-muted mb-0 small">
                                            Revisa tu carpeta de spam o correo no deseado. 
                                            El enlace expira en 60 minutos.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pasos de recuperación -->
                    <div class="mt-4">
                        <div class="card">
                            <div class="card-body p-3">
                                <h6 class="text-center mb-3 text-muted">Proceso de Recuperación</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center mb-2">
                                                <span class="text-white fw-bold">1</span>
                                            </div>
                                            <small class="text-muted">Ingresa tu email</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center mb-2">
                                                <span class="text-white fw-bold">2</span>
                                            </div>
                                            <small class="text-muted">Recibe el enlace</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center mb-2">
                                                <span class="text-white fw-bold">3</span>
                                            </div>
                                            <small class="text-muted">Restablece contraseña</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enlaces adicionales -->
                    <div class="mt-4 text-center">
                        <p class="mb-0">
                            ¿Recordaste tu contraseña? 
                            <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline">Inicia sesión</a>
                        </p>
                    </div>

                    <!-- Contacto de soporte -->
                    <div class="mt-3 text-center">
                        <p class="text-muted small">
                            ¿Necesitas ayuda? 
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
// Auto-focus en el campo email
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.focus();
    }
});

// Validación en tiempo real
document.getElementById('email').addEventListener('input', function() {
    const email = this.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>
@endsection