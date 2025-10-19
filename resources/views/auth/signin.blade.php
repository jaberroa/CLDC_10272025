@extends('layouts.auth')

@section('title', 'Iniciar Sesión | CLDCI')

@section('content')

<div class="container">
  <div class="row justify-content-center align-items-center min-vh-100 pt-20 pb-10">
    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
      <div class="card mx-xxl-8 shadow-none">
        <div class="card-body p-8">
          <h3 class="fw-medium text-center">¡Bienvenido de vuelta!</h3>
          <p class="mb-8 text-muted text-center">Inicia sesión para continuar con CLDCI</p>
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" id="redirect_module" name="redirect_module" value="">
            <div class="mb-4">
              <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Ingresa tu email" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-4">
              <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
              <div class="position-relative">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted toggle-password" id="toggle-password" data-target="password"><i class="ri-eye-off-line align-middle"></i></button>
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="my-6">
              <div class="d-flex justify-content-between align-items-center">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                  <label class="form-check-label" for="rememberMe">Recordarme</label>
                </div>
                <div class="form-text">
                  <a href="{{ route('password.request') }}" class="link">¿Olvidaste tu contraseña?</a>
                </div>
              </div>
            </div>
            <div>
              <button type="submit" class="btn btn-primary w-100 mb-4">Iniciar Sesión</button>
              <button type="button" class="btn btn-outline-light w-100 d-flex align-items-center gap-2 justify-content-center text-muted">
                <img src="{{ asset('assets/images/google.png') }}" alt="Google Logo" class="h-20px w-20px">Iniciar con Google
              </button>
            </div>
          </form>
          <p class="text-center mt-6 mb-0 text-muted fs-13">¿No tienes una cuenta? <a href="{{ route('register') }}" class="link fw-semibold">Regístrate aquí</a></p>
        </div>
      </div>
      <p class="position-relative text-center fs-13 mb-0">©
        <script>document.write(new Date().getFullYear())</script> CLDCI. Sistema de Gestión Institucional
      </p>
    </div>
  </div>
</div>

<!-- Accesos Rápidos Separados -->
<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
      <div class="card mx-xxl-8 shadow-none">
        <div class="card-body p-4">
          <h6 class="text-center mb-3 text-muted">Acceso Rápido</h6>
          <div class="row g-2">
            <!-- Botón Dashboard -->
            <div class="col-4">
              <button onclick="handleQuickAccess('Dashboard', '/dashboard')" class="btn btn-outline-primary btn-sm w-100 d-flex flex-column align-items-center py-2">
                <i class="ri-dashboard-3-line fs-4 mb-1"></i>
                <small>Dashboard</small>
              </button>
            </div>
            <!-- Botón Miembros -->
            <div class="col-4">
              <button onclick="handleQuickAccess('Miembros', '/miembros')" class="btn btn-outline-info btn-sm w-100 d-flex flex-column align-items-center py-2">
                <i class="ri-group-line fs-4 mb-1"></i>
                <small>Miembros</small>
              </button>
            </div>
            <!-- Botón Directiva -->
            <div class="col-4">
              <button onclick="handleQuickAccess('Directiva', '/directiva')" class="btn btn-outline-success btn-sm w-100 d-flex flex-column align-items-center py-2">
                <i class="ri-government-line fs-4 mb-1"></i>
                <small>Directiva</small>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
<script src="{{ asset('assets/js/auth/auth.init.js') }}"></script>
<script>
// Función para mostrar toast y redirigir
function showQuickAccessToast(module, url) {
    console.log(`🎯 Acceso rápido seleccionado: ${module}`);
    
    // Verificar que los elementos existan
    const redirectField = document.getElementById('redirect_module');
    const emailField = document.getElementById('email');
    
    if (!redirectField) {
        console.error('❌ Campo redirect_module no encontrado');
        alert('Error: Campo redirect_module no encontrado');
        return;
    }
    
    if (!emailField) {
        console.error('❌ Campo email no encontrado');
        alert('Error: Campo email no encontrado');
        return;
    }
    
    // Crear toast con transición suave
    const toastDiv = document.createElement('div');
    toastDiv.className = 'alert alert-success alert-dismissible fade show position-fixed toast-notification fade-in';
    toastDiv.innerHTML = `
        <i class="ri-check-line me-2"></i>
        <strong>Acceso Rápido Configurado</strong><br>
        Credenciales cargadas para ${module}.<br>
        <small>Auto-enviando en 2 segundos...</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert" onclick="closeToast(this)"></button>
    `;
    
    // Agregar al DOM
    document.body.appendChild(toastDiv);
    console.log('✅ Toast agregado al DOM');
    
    // Configurar redirección después del login
    redirectField.value = module;
    console.log(`✅ Campo redirect_module configurado con: "${module}"`);
    
    // Enfocar el campo de email
    emailField.focus();
    console.log('✅ Campo email enfocado');
    
    // Mostrar confirmación visual
    console.log(`✅ Redirección configurada para: ${module}`);
    
    // Transición de desvanecimiento después de 3 segundos
    setTimeout(() => {
        if (toastDiv.parentNode) {
            console.log('🔄 Iniciando transición de salida del toast');
            // Aplicar clase de salida
            toastDiv.classList.remove('fade-in');
            toastDiv.classList.add('fade-out');
            
            // Remover del DOM después de la transición
            setTimeout(() => {
                if (toastDiv.parentNode) {
                    toastDiv.remove();
                    console.log('✅ Toast eliminado del DOM');
                }
            }, 500);
        }
    }, 3000);
}

// Función para manejar accesos rápidos
function handleQuickAccess(module, url) {
    console.log(`🎯 Acceso rápido seleccionado: ${module}`);
    
    // Credenciales de acceso rápido
    const quickCredentials = {
        'Dashboard': { email: 'admin@cldci.org', password: 'admin123' },
        'Miembros': { email: 'miembros@cldci.org', password: 'miembros123' },
        'Directiva': { email: 'directiva@cldci.org', password: 'directiva123' }
    };
    
    // Obtener credenciales para el módulo
    const credentials = quickCredentials[module];
    if (!credentials) {
        console.error('❌ Credenciales no encontradas para el módulo:', module);
        showQuickAccessToast(module, url);
        return;
    }
    
    // Llenar campos del formulario
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    const redirectField = document.getElementById('redirect_module');
    
    if (emailField && passwordField && redirectField) {
        emailField.value = credentials.email;
        passwordField.value = credentials.password;
        redirectField.value = module;
        
        console.log(`✅ Credenciales configuradas para ${module}: ${credentials.email}`);
        
        // Mostrar toast de confirmación
        showQuickAccessToast(module, url);
        
        // Auto-enviar formulario después de 2 segundos
        setTimeout(() => {
            console.log(`🚀 Auto-enviando formulario para ${module}`);
            document.getElementById('loginForm').submit();
        }, 2000);
    } else {
        console.error('❌ Campos del formulario no encontrados');
        showQuickAccessToast(module, url);
    }
}

// Función para redirección directa (si el usuario ya está autenticado)
function redirectDirectly(url) {
    console.log(`🚀 Redirección directa a: ${url}`);
    window.location.href = url;
}

// Función para cerrar toast manualmente
function closeToast(button) {
    const toastDiv = button.closest('.toast-notification');
    if (toastDiv) {
        toastDiv.classList.remove('fade-in');
        toastDiv.classList.add('fade-out');
        
        setTimeout(() => {
            if (toastDiv.parentNode) {
                toastDiv.remove();
            }
        }, 500);
    }
}

// Función alternativa para redirección directa (si el usuario ya está autenticado)
function redirectDirectly(url) {
    window.location.href = url;
}
</script>
@endsection
