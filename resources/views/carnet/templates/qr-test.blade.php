<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnet Digital - {{ $miembro->nombre_completo }}</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        
        .carnet-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .carnet-header {
            background: {{ $personalizado?->color_primario ?? '#008080' }};
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .carnet-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.8);
        }
        
        .carnet-info {
            flex: 1;
        }
        
        .carnet-name {
            font-size: {{ $personalizado?->tamaño_nombre ?? 18 }}px;
            font-weight: {{ $personalizado?->nombre_negrita ? 'bold' : 'normal' }};
            font-style: {{ $personalizado?->nombre_cursiva ? 'italic' : 'normal' }};
            margin: 0 0 5px 0;
        }
        
        .carnet-profession {
            font-size: {{ $personalizado?->tamaño_profesion ?? 14 }}px;
            font-weight: {{ $personalizado?->profesion_negrita ? 'bold' : 'normal' }};
            font-style: {{ $personalizado?->profesion_cursiva ? 'italic' : 'normal' }};
            opacity: 0.9;
            margin: 0 0 5px 0;
        }
        
        .carnet-organization {
            font-size: {{ $personalizado?->tamaño_organizacion ?? 12 }}px;
            opacity: 0.8;
            margin: 0;
        }
        
        .carnet-body {
            background: {{ $personalizado?->color_fondo ?? '#ffffff' }};
            color: {{ $personalizado?->color_texto ?? '#000000' }};
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .carnet-qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .carnet-qr {
            width: 80px;
            height: 80px;
            background: white;
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .carnet-qr canvas {
            max-width: 100%;
            max-height: 100%;
            border-radius: 4px;
        }
        
        .carnet-website {
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        .carnet-details {
            text-align: right;
        }
        
        .carnet-number {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .carnet-status-badge {
            display: inline-block;
            padding: 4px 12px;
            background: {{ $personalizado?->color_primario ?? '#008080' }};
            color: white;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .carnet-dates {
            font-size: 12px;
            color: #666;
        }
        
        .carnet-date-item {
            margin-bottom: 5px;
        }
        
        .carnet-date-label {
            font-weight: normal;
        }
        
        .carnet-date-value {
            font-weight: bold;
            color: {{ $personalizado?->color_primario ?? '#008080' }};
        }
        
        @media print {
            body { margin: 0; background: white; }
            .carnet-container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="carnet-container">
        <!-- Header con foto y datos principales -->
        <div class="carnet-header">
            <img src="{{ $miembro->foto_url ? asset($miembro->foto_url) : asset('assets/images/default-avatar.png') }}" 
                 alt="Foto de {{ $miembro->nombre_completo }}" 
                 class="carnet-photo">
            
            <div class="carnet-info">
                <div class="carnet-name">{{ $miembro->nombre_completo }}</div>
                <div class="carnet-profession">{{ $miembro->profesion }}</div>
                <div class="carnet-organization">{{ $miembro->organizacion->nombre ?? 'CLDCI Nacional' }}</div>
            </div>
        </div>
        
        <!-- Body con QR y detalles -->
        <div class="carnet-body">
            <div class="carnet-qr-section">
                <div class="carnet-qr" id="carnet-qr-code">
                    <!-- QR Code se genera con JavaScript -->
                </div>
                <div class="carnet-website">www.cldci.org.do</div>
            </div>
            
            <div class="carnet-details">
                <div class="carnet-number">{{ $miembro->numero_carnet }}</div>
                <div class="carnet-status-badge">ACTIVA</div>
                
                <div class="carnet-dates">
                    <div class="carnet-date-item">
                        <span class="carnet-date-label">Miembro Desde:</span>
                        <span class="carnet-date-value">{{ $miembro->fecha_ingreso->format('Y') }}</span>
                    </div>
                    <div class="carnet-date-item">
                        <span class="carnet-date-label">Válido hasta:</span>
                        <span class="carnet-date-value">{{ $miembro->fecha_ingreso->addYears(2)->format('Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para generar QR -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        // Generar QR Code
        document.addEventListener('DOMContentLoaded', function() {
            const qrContainer = document.getElementById('carnet-qr-code');
            if (qrContainer) {
                const qrData = {
                    miembro_id: '{{ $miembro->id }}',
                    numero_carnet: '{{ $miembro->numero_carnet }}',
                    nombre: '{{ $miembro->nombre_completo }}',
                    url: '{{ url("/miembros/{$miembro->id}") }}',
                    timestamp: new Date().toISOString()
                };
                
                console.log('Generando QR con datos:', qrData);
                
                QRCode.toCanvas(qrContainer, JSON.stringify(qrData), {
                    width: 80,
                    height: 80,
                    margin: 1,
                    color: {
                        dark: '#000000',
                        light: '#ffffff'
                    },
                    errorCorrectionLevel: 'M'
                })
                .then(() => {
                    console.log('QR Code generado exitosamente');
                })
                .catch(error => {
                    console.error('Error generando QR:', error);
                    qrContainer.innerHTML = '<div style="text-align: center; color: #666; font-size: 10px;">QR no disponible</div>';
                });
            } else {
                console.error('Contenedor QR no encontrado');
            }
        });
    </script>
</body>
</html>


