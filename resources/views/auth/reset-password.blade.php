@extends('partials.layouts.master')

@section('title', 'Restablecer Contraseña | CLDCI - Sistema de Gestión')

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
                                    <div class="avatar-title rounded-circle bg-light text-success display-4">
                                        <i class="ri-lock-unlock-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-primary">Restablecer Contraseña</h5>
                                <p class="text-muted">Ingresa tu nueva contraseña para completar el proceso.</p>
                            </div>
                            
                            <div class="p-2 mt-4">
                                <form action="{{ route('password.reset') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ $email ?? old('email') }}" 
                                               readonly>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror" 
                                                   name="password" placeholder="Mínimo 8 caracteres" 
                                                   id="password" required autocomplete="new-password">
                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                    type="button" id="password-addon">
                                                <i class="ri-eye-fill align-middle"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input type="password" class="form-control pe-5 password-input @error('password_confirmation') is-invalid @enderror" 
                                                   name="password_confirmation" placeholder="Repite tu nueva contraseña" 
                                                   id="password_confirmation" required autocomplete="new-password">
                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                    type="button" id="password-confirm-addon">
                                                <i class="ri-eye-fill align-middle"></i>
                                            </button>
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Indicador de fortaleza de contraseña -->
                                    <div class="mb-3">
                                        <div class="password-strength">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small class="text-muted">Fortaleza de la contraseña:</small>
                                                <small id="strength-text" class="text-muted">Débil</small>
                                            </div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar" id="strength-bar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button class="btn btn-primary w-100" type="submit">
                                            <i class="ri-check-line me-1"></i>
                                            Restablecer Contraseña
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Requisitos de contraseña -->
                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="text-center mb-3 text-muted">Requisitos de Contraseña</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ri-check-line text-success me-2"></i>
                                            <small class="text-muted">Mínimo 8 caracteres</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ri-check-line text-success me-2"></i>
                                            <small class="text-muted">Al menos 1 número</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ri-check-line text-success me-2"></i>
                                            <small class="text-muted">Al menos 1 mayúscula</small>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ri-check-line text-success me-2"></i>
                                            <small class="text-muted">Al menos 1 símbolo</small>
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
// Toggle password visibility
document.getElementById('password-addon').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('ri-eye-fill');
        icon.classList.add('ri-eye-off-fill');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('ri-eye-off-fill');
        icon.classList.add('ri-eye-fill');
    }
});

document.getElementById('password-confirm-addon').addEventListener('click', function() {
    const passwordInput = document.getElementById('password_confirmation');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('ri-eye-fill');
        icon.classList.add('ri-eye-off-fill');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('ri-eye-off-fill');
        icon.classList.add('ri-eye-fill');
    }
});

// Validación de fortaleza de contraseña
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    
    let strength = 0;
    let strengthLabel = 'Débil';
    let strengthColor = 'bg-danger';
    
    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    if (/[^A-Za-z0-9]/.test(password)) strength += 25;
    
    if (strength >= 75) {
        strengthLabel = 'Fuerte';
        strengthColor = 'bg-success';
    } else if (strength >= 50) {
        strengthLabel = 'Media';
        strengthColor = 'bg-warning';
    }
    
    strengthBar.style.width = strength + '%';
    strengthBar.className = 'progress-bar ' + strengthColor;
    strengthText.textContent = strengthLabel;
});

// Validación de confirmación de contraseña
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});

// Auto-focus en el primer campo
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.focus();
    }
});
</script>
@endsection
