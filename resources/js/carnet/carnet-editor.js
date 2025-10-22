/* ==========================================
   Carnet Editor - JavaScript Simplificado
   ========================================== */

// Variables globales
let carnetMiembro = null;
let carnetTemplate = null;
let carnetPersonalizado = null;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos del miembro desde variables globales
    if (typeof window.carnetMiembro !== 'undefined') {
        carnetMiembro = window.carnetMiembro;
        carnetTemplate = window.carnetTemplate;
        carnetPersonalizado = window.carnetPersonalizado;
        
        // Configurar event listeners
        setupEventListeners();
        
        // Generar QR inicial
        generateQR();
    }
});

function setupEventListeners() {
    // Event listeners para colores
    document.querySelectorAll('input[name="color_primario"], input[name="color_secundario"], input[name="color_fondo"], input[name="color_texto"]').forEach(input => {
        input.addEventListener('input', updateColors);
    });
    
    // Event listeners para tipografía
    document.querySelectorAll('input[name="tamaño_nombre"], input[name="tamaño_profesion"], input[name="tamaño_organizacion"], select[name="fuente_familia"]').forEach(input => {
        input.addEventListener('change', updateTypography);
    });
    
    // Event listeners para datos
    document.querySelectorAll('input[name="nombre"], input[name="profesion"], input[name="organizacion"], input[name="numero_carnet"]').forEach(input => {
        input.addEventListener('input', updateData);
    });
    
    // Event listeners para checkboxes
    document.querySelectorAll('input[name="nombre_negrita"], input[name="nombre_cursiva"], input[name="profesion_negrita"], input[name="profesion_cursiva"]').forEach(input => {
        input.addEventListener('change', updateTypography);
    });
    
    // Event listeners para paleta de colores
    document.querySelectorAll('.carnet-color-option').forEach(option => {
        option.addEventListener('click', function() {
            selectColorPalette(this);
        });
    });
}

function updateColors() {
    const colorPrimario = document.querySelector('input[name="color_primario"]')?.value;
    const colorSecundario = document.querySelector('input[name="color_secundario"]')?.value;
    const colorFondo = document.querySelector('input[name="color_fondo"]')?.value;
    const colorTexto = document.querySelector('input[name="color_texto"]')?.value;

    const preview = document.querySelector('#carnet-preview');
    if (preview) {
        // Aplicar colores directamente a los elementos
        const header = preview.querySelector('.carnet-header');
        if (header) {
            header.style.background = colorPrimario;
        }
        
        const body = preview.querySelector('.carnet-body');
        if (body) {
            body.style.background = colorFondo;
            body.style.color = colorTexto;
        }
        
        // Aplicar colores a elementos específicos
        const statusBadge = preview.querySelector('.carnet-status-badge');
        if (statusBadge) {
            statusBadge.style.background = colorPrimario;
        }
        
        const dateValues = preview.querySelectorAll('.carnet-date-value');
        dateValues.forEach(element => {
            element.style.color = colorPrimario;
        });
    }
}

function updateTypography() {
    const nombreSize = document.querySelector('input[name="tamaño_nombre"]')?.value;
    const profesionSize = document.querySelector('input[name="tamaño_profesion"]')?.value;
    const organizacionSize = document.querySelector('input[name="tamaño_organizacion"]')?.value;
    const fontFamily = document.querySelector('select[name="fuente_familia"]')?.value;
    const nombreBold = document.querySelector('input[name="nombre_negrita"]')?.checked;
    const nombreItalic = document.querySelector('input[name="nombre_cursiva"]')?.checked;
    const profesionBold = document.querySelector('input[name="profesion_negrita"]')?.checked;
    const profesionItalic = document.querySelector('input[name="profesion_cursiva"]')?.checked;

    const preview = document.querySelector('#carnet-preview');
    if (preview) {
        preview.style.fontFamily = fontFamily;
        
        const nombreElement = preview.querySelector('#carnet-nombre-preview');
        if (nombreElement) {
            nombreElement.style.fontSize = `${nombreSize}px`;
            nombreElement.style.fontWeight = nombreBold ? 'bold' : 'normal';
            nombreElement.style.fontStyle = nombreItalic ? 'italic' : 'normal';
        }
        
        const profesionElement = preview.querySelector('#carnet-profesion-preview');
        if (profesionElement) {
            profesionElement.style.fontSize = `${profesionSize}px`;
            profesionElement.style.fontWeight = profesionBold ? 'bold' : 'normal';
            profesionElement.style.fontStyle = profesionItalic ? 'italic' : 'normal';
        }
        
        const organizacionElement = preview.querySelector('#carnet-organizacion-preview');
        if (organizacionElement) {
            organizacionElement.style.fontSize = `${organizacionSize}px`;
        }
    }
}

function updateData() {
    const nombre = document.querySelector('input[name="nombre"]')?.value;
    const profesion = document.querySelector('input[name="profesion"]')?.value;
    const organizacion = document.querySelector('input[name="organizacion"]')?.value;
    const numeroCarnet = document.querySelector('input[name="numero_carnet"]')?.value;

    const preview = document.querySelector('#carnet-preview');
    if (preview) {
        const nombreElement = preview.querySelector('#carnet-nombre-preview');
        const profesionElement = preview.querySelector('#carnet-profesion-preview');
        const organizacionElement = preview.querySelector('#carnet-organizacion-preview');
        const numeroElement = preview.querySelector('#carnet-numero-preview');

        if (nombreElement) nombreElement.textContent = nombre;
        if (profesionElement) profesionElement.textContent = profesion;
        if (organizacionElement) organizacionElement.textContent = organizacion;
        if (numeroElement) numeroElement.textContent = numeroCarnet;
    }
}

function selectColorPalette(colorOption) {
    // Remover selección anterior
    document.querySelectorAll('.carnet-color-option').forEach(option => {
        option.classList.remove('active');
    });

    // Seleccionar nueva paleta
    colorOption.classList.add('active');
    
    const primaryColor = colorOption.dataset.primary;
    const secondaryColor = colorOption.dataset.secondary;

    if (primaryColor) {
        const primaryInput = document.querySelector('input[name="color_primario"]');
        if (primaryInput) primaryInput.value = primaryColor;
    }

    if (secondaryColor) {
        const secondaryInput = document.querySelector('input[name="color_secundario"]');
        if (secondaryInput) secondaryInput.value = secondaryColor;
    }

    updateColors();
}

function generateQR() {
    const qrContainer = document.querySelector('#carnet-qr-code');
    if (!qrContainer) return;

    const qrData = {
        miembro_id: carnetMiembro?.id,
        numero_carnet: document.querySelector('input[name="numero_carnet"]')?.value || carnetMiembro?.numero_carnet,
        nombre: document.querySelector('input[name="nombre"]')?.value || carnetMiembro?.nombre_completo,
        url: `${window.location.origin}/miembros/${carnetMiembro?.id}`,
        timestamp: new Date().toISOString()
    };
    
    console.log('Generando QR con datos:', qrData);
    
    // Limpiar contenedor
    qrContainer.innerHTML = '';
    
    // Usar QRCode.js si está disponible
    if (typeof QRCode !== 'undefined') {
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
            console.log('QR Code generado exitosamente');
        })
        .catch(error => {
            console.error('Error generando QR:', error);
            qrContainer.innerHTML = '<div style="text-align: center; color: #666; font-size: 16px;">QR no disponible</div>';
        });
    } else {
        qrContainer.innerHTML = '<div style="text-align: center; color: #666; font-size: 16px;">QR no disponible</div>';
    }
}

function savePersonalization() {
    const miembroId = carnetMiembro?.id;
    if (!miembroId) {
        showNotification('Error: No se encontró el ID del miembro', 'error');
        return;
    }

    const data = {
        template_id: carnetTemplate?.id,
        color_primario: document.querySelector('input[name="color_primario"]')?.value,
        color_secundario: document.querySelector('input[name="color_secundario"]')?.value,
        color_fondo: document.querySelector('input[name="color_fondo"]')?.value,
        color_texto: document.querySelector('input[name="color_texto"]')?.value,
        fuente_familia: document.querySelector('select[name="fuente_familia"]')?.value,
        tamaño_nombre: document.querySelector('input[name="tamaño_nombre"]')?.value,
        tamaño_profesion: document.querySelector('input[name="tamaño_profesion"]')?.value,
        tamaño_organizacion: document.querySelector('input[name="tamaño_organizacion"]')?.value,
        nombre_negrita: document.querySelector('input[name="nombre_negrita"]')?.checked,
        nombre_cursiva: document.querySelector('input[name="nombre_cursiva"]')?.checked,
        profesion_negrita: document.querySelector('input[name="profesion_negrita"]')?.checked,
        profesion_cursiva: document.querySelector('input[name="profesion_cursiva"]')?.checked,
        datos_personalizados: {
            nombre: document.querySelector('input[name="nombre"]')?.value,
            profesion: document.querySelector('input[name="profesion"]')?.value,
            organizacion: document.querySelector('input[name="organizacion"]')?.value,
            numero_carnet: document.querySelector('input[name="numero_carnet"]')?.value
        }
    };

    fetch(`/carnet/${miembroId}/personalizar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Personalización guardada correctamente', 'success');
        } else {
            showNotification('Error guardando personalización', 'error');
        }
    })
    .catch(error => {
        console.error('Error guardando personalización:', error);
        showNotification('Error guardando personalización', 'error');
    });
}

function generateCarnet() {
    const miembroId = carnetMiembro?.id;
    const templateId = carnetTemplate?.id;

    if (!miembroId || !templateId) {
        showNotification('Selecciona una plantilla primero', 'warning');
        return;
    }

    // Abrir carnet en nueva ventana
    const url = `/carnet/${miembroId}/generar/${templateId}`;
    window.open(url, '_blank');
}

function generatePDF() {
    const carnetElement = document.querySelector('#carnet-preview');
    if (!carnetElement) {
        showNotification('Error: No se encontró el elemento del carnet', 'error');
        return;
    }

    // Mostrar progreso
    showPDFProgress();

    // Usar html2canvas si está disponible
    if (typeof html2canvas !== 'undefined' && typeof jsPDF !== 'undefined') {
        html2canvas(carnetElement, {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: null
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            const imgWidth = 210; // A4 width in mm
            const pageHeight = 295; // A4 height in mm
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            let heightLeft = imgHeight;

            let position = 0;

            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            // Descargar PDF
            const fileName = `carnet-${carnetMiembro?.id || 'miembro'}-${Date.now()}.pdf`;
            pdf.save(fileName);

            hidePDFProgress();
            showNotification('PDF generado exitosamente', 'success');
        }).catch(error => {
            console.error('Error generando PDF:', error);
            hidePDFProgress();
            showNotification('Error generando PDF. Intenta nuevamente.', 'error');
        });
    } else {
        hidePDFProgress();
        showNotification('Error: Librerías de PDF no disponibles', 'error');
    }
}

function showPDFProgress() {
    const progress = document.querySelector('.carnet-pdf-progress');
    if (progress) {
        progress.classList.add('show');
    }
}

function hidePDFProgress() {
    const progress = document.querySelector('.carnet-pdf-progress');
    if (progress) {
        progress.classList.remove('show');
    }
}

function showNotification(message, type = 'info') {
    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Hacer funciones globales
window.savePersonalization = savePersonalization;
window.generateCarnet = generateCarnet;
window.generatePDF = generatePDF;
