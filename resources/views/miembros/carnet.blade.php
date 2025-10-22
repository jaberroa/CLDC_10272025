@extends('partials.layouts.master')

@section('title', 'Carnet Digital - ' . $miembro->nombre_completo)
@section('title-sub', 'Carnet de Identificación')
@section('pagetitle', 'Carnet Digital')

@section('css')
<link rel="stylesheet" href="{{ vite_asset('resources/css/miembros/app.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/qrcode/qrcode.min.css') }}">
<style>
    .carnet-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
        overflow: hidden;
    }

    .carnet-modal-content {
        background: white;
        border-radius: 20px;
        width: 95vw;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: modalSlideIn 0.4s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(-50px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @keyframes modalSlideOut {
        from {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        to {
            opacity: 0;
            transform: scale(0.8) translateY(-50px);
        }
    }

    .carnet-modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .carnet-modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }

    .carnet-modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .carnet-modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .carnet-modal-body {
        padding: 1.5rem;
        background: #f8f9fa;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .carnet-digital-container {
        max-width: 350px;
        width: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 1.25rem;
        color: white;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
        margin: 0 auto;
    }

    .carnet-digital-container::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }

    .carnet-header {
        text-align: center;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .carnet-logo {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.3);
    }

    .carnet-photo {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.4);
        object-fit: cover;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .carnet-info {
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        padding: 0.75rem;
        margin: 0.5rem 0;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .carnet-qr {
        text-align: center;
        margin: 1rem 0;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 0.75rem;
        backdrop-filter: blur(10px);
    }

    .carnet-footer {
        text-align: center;
        font-size: 0.75rem;
        opacity: 0.9;
        margin-top: 1rem;
        position: relative;
        z-index: 2;
    }

    .carnet-number {
        font-size: 1.1rem;
        font-weight: bold;
        color: #fff;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        letter-spacing: 1px;
    }

    .carnet-name {
        font-size: 1rem;
        font-weight: 700;
        margin: 0.5rem 0 0.25rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }

    .carnet-profession {
        font-size: 0.85rem;
        opacity: 0.9;
        font-weight: 500;
    }

    .carnet-org {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-top: 0.25rem;
    }

    .carnet-status {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .carnet-status.activa {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .carnet-modal-actions {
        padding: 1rem 1.5rem;
        background: white;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .carnet-modal-actions .btn {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        font-size: 0.8rem;
    }

    .carnet-modal-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    @media (max-width: 768px) {
        .carnet-modal-content {
            width: 98vw;
            max-width: 98vw;
            margin: 0.5rem;
        }
        
        .carnet-modal-body {
            padding: 1rem;
            min-height: 250px;
        }
        
        .carnet-digital-container {
            padding: 1rem;
            max-width: 100%;
        }
        
        .carnet-modal-actions {
            padding: 0.75rem;
            flex-direction: column;
        }
        
        .carnet-modal-actions .btn {
            width: 100%;
            margin: 0.25rem 0;
        }
        
        .carnet-photo {
            width: 60px;
            height: 60px;
        }
        
        .carnet-logo {
            width: 40px;
            height: 40px;
        }
    }
</style>
@endsection

@section('content')
<!-- Modal del Carnet Digital -->
<div class="carnet-modal" id="carnetModal">
    <div class="carnet-modal-content">
        <!-- Header del Modal -->
        <div class="carnet-modal-header">
            <h3 class="carnet-modal-title">
                <i class="ri-qr-code-line me-2"></i>
                Carnet Digital - {{ $miembro->nombre_completo }}
            </h3>
            <button class="carnet-modal-close" onclick="cerrarCarnet()">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <!-- Body del Modal -->
        <div class="carnet-modal-body">
            <div class="carnet-digital-container">
                <!-- Header -->
                <div class="carnet-header">
                    <div class="carnet-logo">
                        <i class="ri-mic-line" style="font-size: 1.5rem;"></i>
                    </div>
                    <h4 class="mb-1">CLDCI</h4>
                    <p class="mb-0" style="font-size: 0.7rem; opacity: 0.9;">Círculo de Locutores Dominicanos Colegiados</p>
                </div>

                <!-- Foto del Miembro -->
                <div class="text-center mb-3">
                    @if($miembro->foto_url)
                    <img src="{{ asset($miembro->foto_url) }}" alt="Foto" class="carnet-photo">
                    @else
                    <div class="carnet-photo d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.2);">
                        <i class="ri-user-line" style="font-size: 2rem;"></i>
                    </div>
                    @endif
                </div>

                <!-- Información del Miembro -->
                <div class="carnet-info">
                    <div class="text-center">
                        <div class="carnet-name">{{ $miembro->nombre_completo }}</div>
                        <div class="carnet-profession">{{ $miembro->profesion ?? 'Locutor' }}</div>
                        <div class="carnet-org">{{ $miembro->organizacion->nombre ?? 'CLDCI Nacional' }}</div>
                        <div class="mt-2">
                            <span class="carnet-status activa">Activa</span>
                        </div>
                    </div>
                </div>

                <!-- Número de Carnet -->
                <div class="text-center mb-3">
                    <div style="font-size: 0.7rem; opacity: 0.8; margin-bottom: 0.25rem;">Número de Carnet</div>
                    <div class="carnet-number">{{ $miembro->numero_carnet }}</div>
                </div>

                <!-- QR Code -->
                <div class="carnet-qr">
                    <div id="qrcode" class="d-flex justify-content-center"></div>
                    <div style="font-size: 0.6rem; opacity: 0.8; margin-top: 0.5rem;">
                        Código de Verificación Digital
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="carnet-info">
                    <div class="row text-center">
                        <div class="col-6">
                            <div style="font-size: 0.6rem; opacity: 0.8; margin-bottom: 0.25rem;">Tipo de Membresía</div>
                            <div style="font-size: 0.8rem; font-weight: 600;">{{ ucfirst($miembro->estadoMembresia->nombre ?? 'Activa') }}</div>
                        </div>
                        <div class="col-6">
                            <div style="font-size: 0.6rem; opacity: 0.8; margin-bottom: 0.25rem;">Miembro Desde</div>
                            <div style="font-size: 0.8rem; font-weight: 600;">{{ $miembro->fecha_ingreso->format('Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="carnet-footer">
                    <div style="font-weight: 600; margin-bottom: 0.25rem;">www.cldci.org.do</div>
                    <div>Válido hasta: {{ $miembro->fecha_ingreso->addYears(2)->format('Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Acciones del Modal -->
        <div class="carnet-modal-actions">
            <button onclick="imprimirCarnet()" class="btn btn-primary">
                <i class="ri-printer-line me-2"></i> Imprimir
            </button>
            <button onclick="descargarCarnet()" class="btn btn-success">
                <i class="ri-download-line me-2"></i> Descargar PDF
            </button>
            <button onclick="compartirCarnet()" class="btn btn-info">
                <i class="ri-share-line me-2"></i> Compartir
            </button>
            <button onclick="cerrarCarnet()" class="btn btn-outline-secondary">
                <i class="ri-close-line me-2"></i> Cerrar
            </button>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/libs/qrcode/qrcode.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    // Generar QR Code
    const qrData = {
        nombre: '{{ $miembro->nombre_completo }}',
        carnet: '{{ $miembro->numero_carnet }}',
        organizacion: '{{ $miembro->organizacion->nombre ?? "CLDCI Nacional" }}',
        tipo: '{{ $miembro->estadoMembresia->nombre ?? "Activa" }}',
        fecha: '{{ $miembro->fecha_ingreso->format('Y-m-d') }}',
        url: '{{ route('miembros.profile', $miembro->id) }}',
        timestamp: new Date().toISOString()
    };

    // Generar QR Code con mejor calidad
    QRCode.toCanvas(document.getElementById('qrcode'), JSON.stringify(qrData), {
        width: 120,
        height: 120,
        margin: 1,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        },
        errorCorrectionLevel: 'M'
    }, function (error) {
        if (error) {
            console.error('Error generando QR:', error);
            // Fallback: mostrar mensaje de error
            document.getElementById('qrcode').innerHTML = '<div class="text-center text-muted">QR no disponible</div>';
        }
    });

    // Funciones de acción
    function cerrarCarnet() {
        const modal = document.getElementById('carnetModal');
        modal.style.animation = 'modalSlideOut 0.3s ease-in';
        setTimeout(() => {
            window.close();
        }, 300);
    }

    function imprimirCarnet() {
        // Ocultar botones para impresión
        const actions = document.querySelector('.carnet-modal-actions');
        const closeBtn = document.querySelector('.carnet-modal-close');
        actions.style.display = 'none';
        closeBtn.style.display = 'none';
        
        // Imprimir
        window.print();
        
        // Restaurar botones
        setTimeout(() => {
            actions.style.display = 'flex';
            closeBtn.style.display = 'flex';
        }, 1000);
    }

    function descargarCarnet() {
        const carnetContainer = document.querySelector('.carnet-digital-container');
        
        html2canvas(carnetContainer, {
            backgroundColor: null,
            scale: 2,
            useCORS: true,
            allowTaint: true
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: [85, 54] // Tamaño de tarjeta de crédito
            });
            
            const imgWidth = 85;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
            pdf.save('carnet-{{ $miembro->numero_carnet }}.pdf');
        }).catch(error => {
            console.error('Error generando PDF:', error);
            alert('Error al generar el PDF. Intenta nuevamente.');
        });
    }

    function compartirCarnet() {
        if (navigator.share) {
            navigator.share({
                title: 'Carnet Digital - {{ $miembro->nombre_completo }}',
                text: 'Mi carnet digital de CLDCI',
                url: window.location.href
            }).catch(error => {
                console.error('Error compartiendo:', error);
                copiarEnlace();
            });
        } else {
            copiarEnlace();
        }
    }

    function copiarEnlace() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            // Mostrar toast de confirmación
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-header bg-success text-white">
                        <i class="ri-check-line me-2"></i>
                        <strong class="me-auto">Enlace copiado</strong>
                    </div>
                    <div class="toast-body">
                        El enlace del carnet ha sido copiado al portapapeles.
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }).catch(error => {
            console.error('Error copiando enlace:', error);
            alert('No se pudo copiar el enlace. Intenta manualmente.');
        });
    }

    // Efectos visuales adicionales
    document.addEventListener('DOMContentLoaded', function() {
        // Animación de entrada
        const carnetContainer = document.querySelector('.carnet-digital-container');
        carnetContainer.style.opacity = '0';
        carnetContainer.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            carnetContainer.style.transition = 'all 0.6s ease';
            carnetContainer.style.opacity = '1';
            carnetContainer.style.transform = 'translateY(0)';
        }, 100);

        // Efecto hover en botones
        const botones = document.querySelectorAll('.carnet-modal-actions .btn');
        botones.forEach(boton => {
            boton.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.05)';
            });
            
            boton.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Cerrar modal con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarCarnet();
            }
        });

        // Cerrar modal al hacer clic fuera del contenido
        document.getElementById('carnetModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarCarnet();
            }
        });
    });
</script>
@endsection

