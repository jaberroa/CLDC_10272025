<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento Protegido - CLDCI</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        
        .password-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            margin: 1rem;
        }
        
        .lock-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .form-control {
            padding: 1rem;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-unlock {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-unlock:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="password-card">
        <div class="lock-icon">
            <i class="ri-lock-line" style="font-size: 40px; color: white;"></i>
        </div>
        
        <h2 class="text-center mb-2">Documento Protegido</h2>
        <p class="text-center text-muted mb-4">
            Este documento está protegido con contraseña. Por favor ingresa la contraseña para continuar.
        </p>
        
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="ri-error-warning-line me-2"></i>
                {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('documentos.compartido.verificar-password', $comparticion->token) }}">
            @csrf
            
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="ri-key-line me-1"></i>
                    Contraseña
                </label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       name="password" 
                       placeholder="Ingresa la contraseña"
                       autofocus
                       required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary btn-unlock">
                <i class="ri-lock-unlock-line me-2"></i>
                Desbloquear Documento
            </button>
        </form>
        
        <div class="text-center mt-4">
            <small class="text-muted">
                <i class="ri-shield-check-line me-1"></i>
                Conexión segura mediante CLDCI
            </small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

