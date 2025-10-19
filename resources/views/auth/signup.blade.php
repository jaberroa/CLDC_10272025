@extends('partials.layouts.master')

@section('title', 'Registro | CLDCI - Sistema de Gestión')

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
                                <h5 class="text-primary">¡Únete a CLDCI!</h5>
                                <p class="text-muted">Crea tu cuenta para acceder al sistema.</p>
                            </div>
                            
                            <div class="p-2 mt-4">
                                <form action="{{ route('register') }}" method="POST">
                                    @csrf
                                    
                                    <!-- Información Personal -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                                       id="nombre" name="nombre" placeholder="Tu nombre" 
                                                       value="{{ old('nombre') }}" required>
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                                                       id="apellido" name="apellido" placeholder="Tu apellido" 
                                                       value="{{ old('apellido') }}" required>
                                                @error('apellido')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" placeholder="tu@email.com" 
                                               value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control @error('telefono') is-invalid @enderror" 
                                               id="telefono" name="telefono" placeholder="+1 (809) 123-4567" 
                                               value="{{ old('telefono') }}">
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="cedula" class="form-label">Cédula <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('cedula') is-invalid @enderror" 
                                               id="cedula" name="cedula" placeholder="000-0000000-0" 
                                               value="{{ old('cedula') }}" required>
                                        @error('cedula')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Información Profesional -->
                                    <div class="mb-3">
                                        <label for="profesion" class="form-label">Profesión <span class="text-danger">*</span></label>
                                        <select class="form-select @error('profesion') is-invalid @enderror" 
                                                id="profesion" name="profesion" required>
                                            <option value="">Selecciona tu profesión</option>
                                            <option value="locutor" {{ old('profesion') == 'locutor' ? 'selected' : '' }}>Locutor</option>
                                            <option value="periodista" {{ old('profesion') == 'periodista' ? 'selected' : '' }}>Periodista</option>
                                            <option value="comunicador" {{ old('profesion') == 'comunicador' ? 'selected' : '' }}>Comunicador Social</option>
                                            <option value="productor" {{ old('profesion') == 'productor' ? 'selected' : '' }}>Productor</option>
                                            <option value="conductor" {{ old('profesion') == 'conductor' ? 'selected' : '' }}>Conductor</option>
                                            <option value="otro" {{ old('profesion') == 'otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        @error('profesion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="organizacion" class="form-label">Seccional</label>
                                        <select class="form-select @error('organizacion_id') is-invalid @enderror" 
                                                id="organizacion" name="organizacion_id">
                                            <option value="">Selecciona tu seccional</option>
                                            <option value="1" {{ old('organizacion_id') == '1' ? 'selected' : '' }}>Santo Domingo</option>
                                            <option value="2" {{ old('organizacion_id') == '2' ? 'selected' : '' }}>Santiago</option>
                                            <option value="3" {{ old('organizacion_id') == '3' ? 'selected' : '' }}>La Romana</option>
                                            <option value="4" {{ old('organizacion_id') == '4' ? 'selected' : '' }}>San Pedro de Macorís</option>
                                            <option value="5" {{ old('organizacion_id') == '5' ? 'selected' : '' }}>Puerto Plata</option>
                                            <option value="6" {{ old('organizacion_id') == '6' ? 'selected' : '' }}>Higüey</option>
                                            <option value="7" {{ old('organizacion_id') == '7' ? 'selected' : '' }}>San Cristóbal</option>
                                            <option value="8" {{ old('organizacion_id') == '8' ? 'selected' : '' }}>Bonao</option>
                                        </select>
                                        @error('organizacion_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Contraseña -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
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
                                        <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input type="password" class="form-control pe-5 password-input @error('password_confirmation') is-invalid @enderror" 
                                                   name="password_confirmation" placeholder="Repite tu contraseña" 
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

                                    <!-- Términos y Condiciones -->
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            Acepto los <a href="#" class="text-primary">términos y condiciones</a> y la <a href="#" class="text-primary">política de privacidad</a>
                                        </label>
                                    </div>

                                    <div class="mt-4">
                                        <button class="btn btn-primary w-100" type="submit">Crear Cuenta</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <h6 class="text-center mb-3 text-muted">Beneficios de ser miembro CLDCI</h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <i class="ri-award-line fs-3 text-primary mb-2"></i>
                                            <small class="text-muted">Certificación Profesional</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex flex-column">
                                            <i class="ri-group-line fs-3 text-info mb-2"></i>
                                            <small class="text-muted">Red Profesional</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enlaces adicionales -->
                    <div class="mt-4 text-center">
                        <p class="mb-0">¿Ya tienes una cuenta? 
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

// Auto-focus en el primer campo
document.addEventListener('DOMContentLoaded', function() {
    const nombreInput = document.getElementById('nombre');
    if (nombreInput) {
        nombreInput.focus();
    }
});
</script>
@endsection