<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votación no disponible | CLDCI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            margin: 0 1rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .card-body {
            padding: 2rem;
            text-align: center;
        }
        
        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .no-iniciada .icon {
            color: #ffc107;
        }
        
        .finalizada .icon {
            color: #6c757d;
        }
        
        .no-activa .icon {
            color: #dc3545;
        }
        
        .sin-candidatos .icon {
            color: #17a2b8;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1><i class="ri-vote-line me-2"></i>{{ $eleccion->titulo ?? 'Votación' }}</h1>
                <p>{{ $eleccion->organizacion->nombre ?? 'CLDCI' }}</p>
            </div>
            
            <div class="card-body {{ $tipo }}">
                @if($tipo === 'no-iniciada')
                    <i class="ri-time-line icon"></i>
                    <h3 class="mb-3">Votación no iniciada</h3>
                    <p class="mb-3">{{ $mensaje }}</p>
                    <p class="text-muted">La votación comenzará el: <strong>{{ $fechaInicio }}</strong></p>
                    
                @elseif($tipo === 'finalizada')
                    <i class="ri-checkbox-circle-line icon"></i>
                    <h3 class="mb-3">Votación finalizada</h3>
                    <p class="mb-3">{{ $mensaje }}</p>
                    <p class="text-muted">La votación terminó el: <strong>{{ $fechaFin }}</strong></p>
                    
                @elseif($tipo === 'no-activa')
                    <i class="ri-pause-circle-line icon"></i>
                    <h3 class="mb-3">Votación no disponible</h3>
                    <p class="mb-3">{{ $mensaje }}</p>
                    
                @elseif($tipo === 'sin-candidatos')
                    <i class="ri-user-line icon"></i>
                    <h3 class="mb-3">Sin candidatos</h3>
                    <p class="mb-3">{{ $mensaje }}</p>
                    
                @else
                    <i class="ri-error-warning-line icon"></i>
                    <h3 class="mb-3">Error</h3>
                    <p class="mb-3">{{ $mensaje }}</p>
                @endif
                
                <button type="button" class="btn btn-primary mt-3" onclick="window.close()">
                    <i class="ri-close-line me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</body>
</html>
