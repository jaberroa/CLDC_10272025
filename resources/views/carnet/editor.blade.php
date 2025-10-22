@extends('partials.layouts.master')

@section('title', 'Carnet Digital - Editor')

@section('css')
<link rel="stylesheet" href="{{ vite_asset('resources/css/carnet/app.css') }}">
@endsection

@section('content')
<div class="carnet-editor">
    <div class="carnet-editor-container">
        <!-- Panel de vista previa -->
        <div class="carnet-preview-panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="ri-eye-line me-2"></i>
                    Vista Previa
                </h4>
                <div class="carnet-preview-actions">
                    <button class="btn btn-sm btn-outline-primary" onclick="generatePDF()">
                        <i class="ri-download-line me-1"></i> PDF
                    </button>
                </div>
            </div>
            
            <div class="carnet-preview-container">
                <div class="carnet-base {{ str_replace('.', '-', $template->template_path) }}" id="carnet-preview" style="width: 676px; height: 422px; position: relative; overflow: hidden; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                    <!-- Header del carnet -->
                    <div class="carnet-header" style="background: {{ $personalizado->color_primario ?? '#008080' }}; color: white; padding: 20px; display: flex; align-items: center; gap: 15px; height: 60%;">
                        <img src="{{ $miembro->foto_url ? asset($miembro->foto_url) : asset('assets/images/default-avatar.png') }}" 
                             alt="Foto de {{ $miembro->nombre_completo }}" 
                             class="carnet-photo" id="carnet-photo-preview" style="width: 104px; height: 104px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,0.8);">
                        
                        <div class="carnet-info" style="flex: 1;">
                            <div class="carnet-text nombre" id="carnet-nombre-preview" style="font-size: {{ $personalizado->tamaño_nombre ?? 18 }}px; font-weight: {{ $personalizado->nombre_negrita ? 'bold' : 'normal' }}; font-style: {{ $personalizado->nombre_cursiva ? 'italic' : 'normal' }}; margin: 0 0 5px 0; line-height: 1.2;">{{ $miembro->nombre_completo }}</div>
                            <div class="carnet-text profesion" id="carnet-profesion-preview" style="font-size: {{ $personalizado->tamaño_profesion ?? 14 }}px; font-weight: {{ $personalizado->profesion_negrita ? 'bold' : 'normal' }}; font-style: {{ $personalizado->profesion_cursiva ? 'italic' : 'normal' }}; opacity: 0.9; margin: 0 0 5px 0;">{{ $miembro->profesion }}</div>
                            <div class="carnet-text organizacion" id="carnet-organizacion-preview" style="font-size: {{ $personalizado->tamaño_organizacion ?? 12 }}px; opacity: 0.8; margin: 0;">{{ $miembro->organizacion->nombre ?? 'CLDCI Nacional' }}</div>
                        </div>
                    </div>
                    
                    <!-- Body del carnet -->
                    <div class="carnet-body" style="background: {{ $personalizado->color_fondo ?? '#ffffff' }}; color: {{ $personalizado->color_texto ?? '#000000' }}; padding: 20px; display: flex; justify-content: space-between; align-items: center; height: 40%;">
                        <div class="carnet-qr-section" style="display: flex; flex-direction: column; align-items: center; gap: 13px;">
                            <div class="carnet-qr" id="carnet-qr-code" style="width: 104px; height: 104px; background: white; padding: 6px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center;">
                                <!-- QR Code se genera con JavaScript -->
                                <div style="font-size: 16px; color: #666;">QR</div>
                            </div>
                            <div class="carnet-website" style="font-size: 16px; color: #666; text-align: center;">www.cldci.org.do</div>
                        </div>
                        
                        <div class="carnet-details" style="text-align: right;">
                            <div class="carnet-number" id="carnet-numero-preview" style="font-size: 26px; font-weight: bold; margin-bottom: 16px;">{{ $miembro->numero_carnet }}</div>
                            <div class="carnet-status-badge activa" style="display: inline-block; padding: 8px 20px; background: {{ $personalizado->color_primario ?? '#008080' }}; color: white; border-radius: 26px; font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 16px;">ACTIVA</div>
                            
                            <div class="carnet-dates" style="font-size: 18px; color: #666;">
                                <div class="carnet-date-item" style="margin-bottom: 8px;">
                                    <span class="carnet-date-label" style="font-weight: normal;">Miembro Desde:</span>
                                    <span class="carnet-date-value" style="font-weight: bold; color: {{ $personalizado->color_primario ?? '#008080' }};">{{ $miembro->fecha_ingreso->format('Y') }}</span>
                                </div>
                                <div class="carnet-date-item" style="margin-bottom: 8px;">
                                    <span class="carnet-date-label" style="font-weight: normal;">Válido hasta:</span>
                                    <span class="carnet-date-value" style="font-weight: bold; color: {{ $personalizado->color_primario ?? '#008080' }};">{{ $miembro->fecha_ingreso->addYears(2)->format('Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de personalización -->
        <div class="carnet-customization-panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="ri-settings-3-line me-2"></i>
                    Personalización
                </h4>
                <a href="{{ route('carnet.selector', $miembro->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Volver
                </a>
            </div>

            <!-- Sección de colores -->
            <div class="carnet-section">
                <div class="carnet-section-title">
                    <i class="ri-palette-line"></i>
                    Colores
                </div>
                
                <!-- Paleta de colores predefinidos -->
                <div class="carnet-color-palette mb-3">
                    <div class="carnet-color-option" data-primary="#008080" data-secondary="#20B2AA" style="background: linear-gradient(45deg, #008080, #20B2AA);"></div>
                    <div class="carnet-color-option" data-primary="#2E8B57" data-secondary="#3CB371" style="background: linear-gradient(45deg, #2E8B57, #3CB371);"></div>
                    <div class="carnet-color-option" data-primary="#4682B4" data-secondary="#5F9EA0" style="background: linear-gradient(45deg, #4682B4, #5F9EA0);"></div>
                    <div class="carnet-color-option" data-primary="#8B4513" data-secondary="#A0522D" style="background: linear-gradient(45deg, #8B4513, #A0522D);"></div>
                    <div class="carnet-color-option" data-primary="#800080" data-secondary="#9370DB" style="background: linear-gradient(45deg, #800080, #9370DB);"></div>
                    <div class="carnet-color-option" data-primary="#DC143C" data-secondary="#FF6347" style="background: linear-gradient(45deg, #DC143C, #FF6347);"></div>
                </div>
                
                <!-- Selectores de color -->
                <div class="carnet-color-controls">
                    <div class="carnet-color-input">
                        <label>Color Primario</label>
                        <input type="color" name="color_primario" value="{{ $personalizado->color_primario ?? '#667eea' }}">
                    </div>
                    <div class="carnet-color-input">
                        <label>Color Secundario</label>
                        <input type="color" name="color_secundario" value="{{ $personalizado->color_secundario ?? '#764ba2' }}">
                    </div>
                    <div class="carnet-color-input">
                        <label>Color de Fondo</label>
                        <input type="color" name="color_fondo" value="{{ $personalizado->color_fondo ?? '#ffffff' }}">
                    </div>
                    <div class="carnet-color-input">
                        <label>Color de Texto</label>
                        <input type="color" name="color_texto" value="{{ $personalizado->color_texto ?? '#000000' }}">
                    </div>
                </div>

                <!-- Paleta de colores predefinidos -->
                <div class="carnet-color-palette">
                    <!-- Se llena dinámicamente con JavaScript -->
                </div>
            </div>

            <!-- Sección de tipografía -->
            <div class="carnet-section">
                <div class="carnet-section-title">
                    <i class="ri-text"></i>
                    Tipografía
                </div>
                
                <div class="carnet-typography-controls">
                    <div class="carnet-typography-group">
                        <label>Fuente</label>
                        <select name="fuente_familia">
                            <option value="Arial, sans-serif" {{ ($personalizado->fuente_familia ?? 'Arial, sans-serif') == 'Arial, sans-serif' ? 'selected' : '' }}>Arial</option>
                            <option value="'Helvetica Neue', sans-serif" {{ ($personalizado->fuente_familia ?? '') == "'Helvetica Neue', sans-serif" ? 'selected' : '' }}>Helvetica</option>
                            <option value="'Times New Roman', serif" {{ ($personalizado->fuente_familia ?? '') == "'Times New Roman', serif" ? 'selected' : '' }}>Times New Roman</option>
                            <option value="'Georgia', serif" {{ ($personalizado->fuente_familia ?? '') == "'Georgia', serif" ? 'selected' : '' }}>Georgia</option>
                            <option value="'Courier New', monospace" {{ ($personalizado->fuente_familia ?? '') == "'Courier New', monospace" ? 'selected' : '' }}>Courier New</option>
                        </select>
                    </div>
                </div>

                <div class="carnet-typography-group">
                    <div>
                        <label>Tamaño Nombre</label>
                        <input type="number" name="tamaño_nombre" value="{{ $personalizado->tamaño_nombre ?? 18 }}" min="8" max="48">
                    </div>
                    <div>
                        <label>Tamaño Profesión</label>
                        <input type="number" name="tamaño_profesion" value="{{ $personalizado->tamaño_profesion ?? 14 }}" min="6" max="24">
                    </div>
                </div>

                <div class="carnet-typography-group">
                    <div>
                        <label>Tamaño Organización</label>
                        <input type="number" name="tamaño_organizacion" value="{{ $personalizado->tamaño_organizacion ?? 12 }}" min="6" max="20">
                    </div>
                </div>

                <div class="carnet-checkbox-group">
                    <div class="carnet-checkbox-item">
                        <input type="checkbox" name="nombre_negrita" {{ ($personalizado->nombre_negrita ?? true) ? 'checked' : '' }}>
                        <label>Nombre en Negrita</label>
                    </div>
                    <div class="carnet-checkbox-item">
                        <input type="checkbox" name="nombre_cursiva" {{ ($personalizado->nombre_cursiva ?? false) ? 'checked' : '' }}>
                        <label>Nombre en Cursiva</label>
                    </div>
                </div>

                <div class="carnet-checkbox-group">
                    <div class="carnet-checkbox-item">
                        <input type="checkbox" name="profesion_negrita" {{ ($personalizado->profesion_negrita ?? false) ? 'checked' : '' }}>
                        <label>Profesión en Negrita</label>
                    </div>
                    <div class="carnet-checkbox-item">
                        <input type="checkbox" name="profesion_cursiva" {{ ($personalizado->profesion_cursiva ?? false) ? 'checked' : '' }}>
                        <label>Profesión en Cursiva</label>
                    </div>
                </div>
            </div>

            <!-- Sección de datos -->
            <div class="carnet-section">
                <div class="carnet-section-title">
                    <i class="ri-user-line"></i>
                    Datos del Miembro
                </div>
                
                <div class="carnet-data-controls">
                    <div class="carnet-data-group">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" value="{{ $miembro->nombre_completo }}">
                    </div>
                    
                    <div class="carnet-data-group">
                        <label>Profesión</label>
                        <input type="text" name="profesion" value="{{ $miembro->profesion }}">
                    </div>
                    
                    <div class="carnet-data-group">
                        <label>Organización</label>
                        <input type="text" name="organizacion" value="{{ $miembro->organizacion->nombre ?? 'CLDCI Nacional' }}">
                    </div>
                    
                    <div class="carnet-data-group">
                        <label>Número de Carnet</label>
                        <input type="text" name="numero_carnet" value="{{ $miembro->numero_carnet }}">
                    </div>
                </div>
            </div>

            <!-- Sección de foto -->
            <div class="carnet-section">
                <div class="carnet-section-title">
                    <i class="ri-camera-line"></i>
                    Foto del Miembro
                </div>
                
                <div class="carnet-photo-upload">
                    <div class="carnet-photo-preview mb-3">
                        <img id="carnet-photo-preview" 
                             src="{{ $miembro->foto_url ? asset($miembro->foto_url) : asset('assets/images/default-avatar.png') }}" 
                             alt="Foto del miembro" 
                             class="rounded-circle" 
                             width="80" 
                             height="80"
                             style="object-fit: cover;">
                    </div>
                    
                    <input type="file" id="carnet-photo-input" accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('carnet-photo-input').click()">
                        <i class="ri-upload-line me-1"></i> Cambiar Foto
                    </button>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="carnet-actions">
                <button type="button" class="carnet-btn carnet-btn-primary" onclick="savePersonalization()">
                    <i class="ri-save-line me-1"></i> Guardar Cambios
                </button>
                
                <button type="button" class="carnet-btn carnet-btn-success" onclick="generateCarnet()">
                    <i class="ri-qr-code-line me-1"></i> Generar Carnet
                </button>
                
                <button type="button" class="carnet-btn carnet-btn-outline" onclick="generatePDF()">
                    <i class="ri-download-line me-1"></i> Descargar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para subir foto -->
<div class="modal fade" id="carnet-photo-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir Foto del Miembro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="carnet-photo-crop" src="" alt="Vista previa" class="img-fluid rounded" style="max-height: 300px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="carnetSystem.uploadPhoto()">Subir Foto</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ vite_asset('resources/js/carnet/carnet-editor.js') }}"></script>
<script>
// Datos del miembro para JavaScript
window.carnetMiembro = @json($miembro);
window.carnetTemplate = @json($template);
window.carnetPersonalizado = @json($personalizado);

// Función para generar QR en la vista previa
function generarQRPreview() {
    const qrContainer = document.getElementById('carnet-qr-code');
    
    if (!qrContainer) {
        console.error('Contenedor QR no encontrado');
        return;
    }
    
    if (typeof QRCode === 'undefined') {
        console.error('Librería QRCode no disponible');
        qrContainer.innerHTML = '<div style="text-align: center; color: #666; font-size: 16px;">QR no disponible</div>';
        return;
    }
    
    if (!window.carnetMiembro) {
        console.error('Datos del miembro no disponibles');
        qrContainer.innerHTML = '<div style="text-align: center; color: #666; font-size: 16px;">Datos no disponibles</div>';
        return;
    }
    
    const qrData = {
        miembro_id: window.carnetMiembro.id,
        numero_carnet: window.carnetMiembro.numero_carnet,
        nombre: window.carnetMiembro.nombre_completo,
        url: window.location.origin + '/miembros/' + window.carnetMiembro.id,
        timestamp: new Date().toISOString()
    };
    
    console.log('Generando QR en vista previa con datos:', qrData);
    
    // Limpiar contenedor
    qrContainer.innerHTML = '';
    
    try {
        QRCode.toCanvas(qrContainer, JSON.stringify(qrData), {
            width: 104,
            height: 104,
            margin: 1,
            color: {
                dark: '#000000',
                light: '#ffffff'
            },
            errorCorrectionLevel: 'M'
        })
        .then(() => {
            console.log('QR Code generado exitosamente en vista previa');
        })
        .catch(error => {
            console.error('Error generando QR en vista previa:', error);
            qrContainer.innerHTML = '<div style="text-align: center; color: #666; font-size: 16px;">Error generando QR</div>';
        });
    } catch (error) {
        console.error('Error en try/catch generando QR:', error);
        qrContainer.innerHTML = '<div style="text-align: center; color: #666; font-size: 16px;">Error generando QR</div>';
    }
}

// Función para crear un QR simple como fallback
function crearQRSimple(container) {
    if (!container) return;
    
    const qrData = window.carnetMiembro ? {
        id: window.carnetMiembro.id,
        carnet: window.carnetMiembro.numero_carnet,
        nombre: window.carnetMiembro.nombre_completo
    } : { id: 'N/A', carnet: 'N/A', nombre: 'N/A' };
    
    // Crear un patrón simple de QR usando CSS
    container.innerHTML = `
        <div style="
            width: 104px; 
            height: 104px; 
            background: white; 
            border: 2px solid #000; 
            display: grid; 
            grid-template-columns: repeat(8, 1fr); 
            grid-template-rows: repeat(8, 1fr);
            gap: 1px;
            padding: 2px;
        ">
            <div style="background: #000; grid-column: 1/3; grid-row: 1/3;"></div>
            <div style="background: #000; grid-column: 7/9; grid-row: 1/3;"></div>
            <div style="background: #000; grid-column: 1/3; grid-row: 7/9;"></div>
            <div style="background: #000; grid-column: 3/7; grid-row: 3/7;"></div>
            <div style="background: #000; grid-column: 7/9; grid-row: 7/9;"></div>
        </div>
        <div style="text-align: center; color: #666; font-size: 10px; margin-top: 5px;">
            QR Simple
        </div>
    `;
    
    console.log('QR simple creado como fallback');
}

// Función para verificar y cargar QRCode
function cargarQRCode() {
    return new Promise((resolve, reject) => {
        // Verificar si ya existe
        if (typeof QRCode !== 'undefined') {
            console.log('Librería QRCode ya disponible');
            resolve();
            return;
        }
        
        // Verificar si ya se está cargando
        if (document.querySelector('script[src*="qrcode"]')) {
            console.log('Librería QRCode ya se está cargando');
            // Esperar a que termine de cargar
            const checkQRCode = setInterval(() => {
                if (typeof QRCode !== 'undefined') {
                    clearInterval(checkQRCode);
                    resolve();
                }
            }, 100);
            
            // Timeout después de 5 segundos
            setTimeout(() => {
                clearInterval(checkQRCode);
                reject(new Error('Timeout cargando QRCode'));
            }, 5000);
            return;
        }
        
        // Cargar librería QRCode
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
        script.async = true;
        script.onload = function() {
            console.log('Librería QRCode cargada exitosamente desde CDN');
            resolve();
        };
        script.onerror = function() {
            console.error('Error cargando QRCode desde CDN, intentando CDN alternativo...');
            
            // Intentar con CDN alternativo
            const script2 = document.createElement('script');
            script2.src = 'https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js';
            script2.async = true;
            script2.onload = function() {
                console.log('Librería QRCode cargada exitosamente desde CDN alternativo');
                resolve();
            };
            script2.onerror = function() {
                console.error('Error cargando QRCode desde CDN alternativo');
                reject(new Error('Error cargando QRCode desde todos los CDN'));
            };
            document.head.appendChild(script2);
        };
        document.head.appendChild(script);
    });
}

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, iniciando carga de QRCode...');
    
    cargarQRCode()
        .then(() => {
            console.log('QRCode cargado, generando QR...');
            generarQRPreview();
        })
        .catch(error => {
            console.error('Error cargando QRCode:', error);
            const qrContainer = document.getElementById('carnet-qr-code');
            if (qrContainer) {
                // Crear un QR simple como fallback
                crearQRSimple(qrContainer);
            }
        });
});
</script>
@endsection
