<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $comparticion->documento->titulo }} - Documento Compartido</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        .document-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .document-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .document-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
        }
        
        .document-preview {
            padding: 2rem;
            min-height: 600px;
            background: #f8f9fa;
        }
        
        .btn-download {
            background: white;
            color: #667eea;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .document-info {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255,255,255,0.9);
        }
        
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(255,255,255,0.95);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .alert-expiration {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="document-container">
        <!-- Header -->
        <div class="document-card">
            <div class="document-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="mb-2">
                            <i class="ri-file-text-line me-2"></i>
                            {{ $comparticion->documento->titulo }}
                        </h1>
                        @if($comparticion->documento->descripcion)
                            <p class="mb-3 opacity-75">{{ $comparticion->documento->descripcion }}</p>
                        @endif
                        
                        <div class="document-info">
                            <div class="info-item">
                                <i class="ri-file-line"></i>
                                <span>{{ strtoupper($comparticion->documento->extension) }}</span>
                            </div>
                            <div class="info-item">
                                <i class="ri-database-line"></i>
                                <span>{{ number_format($comparticion->documento->tamano_bytes / 1024 / 1024, 2) }} MB</span>
                            </div>
                            <div class="info-item">
                                <i class="ri-calendar-line"></i>
                                <span>{{ $comparticion->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($comparticion->fecha_expiracion)
                                <div class="info-item">
                                    <i class="ri-time-line"></i>
                                    <span>Expira: {{ $comparticion->fecha_expiracion->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($comparticion->puede_descargar)
                        <a href="{{ route('gestion-documental.documentos.descargar', $comparticion->documento) }}" 
                           class="btn btn-download">
                            <i class="ri-download-line me-2"></i>
                            Descargar
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Alertas -->
            @if($comparticion->mensaje)
                <div class="alert alert-info m-3">
                    <i class="ri-information-line me-2"></i>
                    <strong>Mensaje del remitente:</strong> {{ $comparticion->mensaje }}
                </div>
            @endif
            
            @if($comparticion->fecha_expiracion && $comparticion->fecha_expiracion->diffInDays(now()) <= 3)
                <div class="alert-expiration m-3">
                    <i class="ri-alert-line me-2"></i>
                    <strong>Aviso:</strong> Este enlace expirará en {{ $comparticion->fecha_expiracion->diffInDays(now()) }} día(s).
                </div>
            @endif
            
            @if($comparticion->max_accesos)
                <div class="alert alert-warning m-3">
                    <i class="ri-eye-line me-2"></i>
                    Accesos restantes: {{ $comparticion->max_accesos - $comparticion->accesos_actuales }}
                </div>
            @endif
            
            <!-- Preview del Documento -->
            <div class="document-preview">
                @php
                    $extension = strtolower($comparticion->documento->extension);
                @endphp
                
                @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <!-- Preview de Imagen -->
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $comparticion->documento->ruta) }}" 
                             alt="{{ $comparticion->documento->titulo }}" 
                             class="img-fluid rounded shadow"
                             style="max-height: 800px;">
                    </div>
                    
                @elseif($extension === 'pdf')
                    <!-- Preview de PDF -->
                    <iframe src="{{ asset('storage/' . $comparticion->documento->ruta) }}" 
                            width="100%" 
                            height="800px"
                            style="border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </iframe>
                    
                @else
                    <!-- No hay preview disponible -->
                    <div class="text-center py-5">
                        @php
                            $iconos = [
                                'doc' => ['icono' => 'ri-file-word-line', 'color' => '#0d6efd'],
                                'docx' => ['icono' => 'ri-file-word-line', 'color' => '#0d6efd'],
                                'xls' => ['icono' => 'ri-file-excel-line', 'color' => '#198754'],
                                'xlsx' => ['icono' => 'ri-file-excel-line', 'color' => '#198754'],
                                'ppt' => ['icono' => 'ri-file-ppt-line', 'color' => '#fd7e14'],
                                'pptx' => ['icono' => 'ri-file-ppt-line', 'color' => '#fd7e14'],
                            ];
                            $info = $iconos[$extension] ?? ['icono' => 'ri-file-line', 'color' => '#6c757d'];
                        @endphp
                        
                        <i class="{{ $info['icono'] }}" style="font-size: 120px; color: {{ $info['color'] }}"></i>
                        <h3 class="mt-4">Vista previa no disponible</h3>
                        <p class="text-muted">Este tipo de archivo no se puede previsualizar en el navegador</p>
                        
                        @if($comparticion->puede_descargar)
                            <a href="{{ route('gestion-documental.documentos.descargar', $comparticion->documento) }}" 
                               class="btn btn-primary btn-lg mt-3">
                                <i class="ri-download-line me-2"></i>
                                Descargar para Ver
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Watermark -->
    <div class="watermark">
        <small class="text-muted d-block">
            <i class="ri-shield-check-line me-1"></i>
            Documento compartido mediante CLDCI
        </small>
        <small class="text-muted d-block mt-1">
            <i class="ri-eye-line me-1"></i>
            Visualizaciones: {{ $comparticion->accesos_actuales }}
        </small>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

