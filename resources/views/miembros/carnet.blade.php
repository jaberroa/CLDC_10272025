<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Carnet Digital - {{ $miembro->nombre_completo }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/icons.min.css') }}">
    <style>
        .carnet-container {
            max-width: 400px;
            margin: 0 auto;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .carnet-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .carnet-logo {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
        .carnet-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.3);
            object-fit: cover;
        }
        .carnet-info {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }
        .carnet-qr {
            text-align: center;
            margin: 20px 0;
        }
        .carnet-footer {
            text-align: center;
            font-size: 12px;
            opacity: 0.8;
            margin-top: 20px;
        }
        .carnet-number {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .carnet-name {
            font-size: 16px;
            font-weight: 600;
            margin: 10px 0 5px;
        }
        .carnet-profession {
            font-size: 14px;
            opacity: 0.9;
        }
        .carnet-org {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 5px;
        }
        @media print {
            body { margin: 0; }
            .carnet-container { 
                max-width: none; 
                margin: 0;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="carnet-container">
            <!-- Header -->
            <div class="carnet-header">
                <div class="carnet-logo">
                    <i class="ri-mic-line" style="font-size: 24px;"></i>
                </div>
                <h4 class="mb-0">CLDCI</h4>
                <p class="mb-0" style="font-size: 12px; opacity: 0.9;">Círculo de Locutores Dominicanos Colegiados</p>
            </div>

            <!-- Foto del Miembro -->
            <div class="text-center mb-3">
                @if($miembro->foto_url)
                <img src="{{ $miembro->foto_url }}" alt="Foto" class="carnet-photo">
                @else
                <div class="carnet-photo d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.2);">
                    <i class="ri-user-line" style="font-size: 32px;"></i>
                </div>
                @endif
            </div>

            <!-- Información del Miembro -->
            <div class="carnet-info">
                <div class="text-center">
                    <div class="carnet-name">{{ $miembro->nombre_completo }}</div>
                    <div class="carnet-profession">{{ $miembro->profesion }}</div>
                    <div class="carnet-org">{{ $miembro->organizacion->nombre }}</div>
                </div>
            </div>

            <!-- Número de Carnet -->
            <div class="text-center mb-3">
                <div style="font-size: 12px; opacity: 0.8; margin-bottom: 5px;">Número de Carnet</div>
                <div class="carnet-number">{{ $miembro->numero_carnet }}</div>
            </div>

            <!-- QR Code -->
            <div class="carnet-qr">
                <div id="qrcode"></div>
                <div style="font-size: 10px; opacity: 0.8; margin-top: 5px;">
                    Código de Verificación
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="carnet-info">
                <div class="row text-center">
                    <div class="col-6">
                        <div style="font-size: 10px; opacity: 0.8;">Tipo</div>
                        <div style="font-size: 12px; font-weight: 600;">{{ ucfirst($miembro->tipo_membresia) }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size: 10px; opacity: 0.8;">Desde</div>
                        <div style="font-size: 12px; font-weight: 600;">{{ $miembro->fecha_ingreso->format('Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="carnet-footer">
                <div>www.cldci.org.do</div>
                <div>Válido hasta: {{ $miembro->fecha_ingreso->addYears(2)->format('Y') }}</div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="text-center mt-4">
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="ri-printer-line me-1"></i> Imprimir
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary">
                <i class="ri-close-line me-1"></i> Cerrar
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/libs/qrcode/qrcode.min.js') }}"></script>
    <script>
        // Generar QR Code
        const qrData = {
            nombre: '{{ $miembro->nombre_completo }}',
            carnet: '{{ $miembro->numero_carnet }}',
            organizacion: '{{ $miembro->organizacion->nombre }}',
            tipo: '{{ $miembro->tipo_membresia }}',
            fecha: '{{ $miembro->fecha_ingreso->format('Y-m-d') }}',
            url: '{{ route('miembros.show', $miembro->id) }}'
        };

        QRCode.toCanvas(document.getElementById('qrcode'), JSON.stringify(qrData), {
            width: 120,
            height: 120,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        }, function (error) {
            if (error) console.error(error);
        });
    </script>
</body>
</html>
