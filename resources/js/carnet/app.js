/* ==========================================
   Sistema de Carnet Digital - JavaScript Principal
   ========================================== */

// Importar dependencias
import QRCode from 'qrcode';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

// Clase principal del sistema de carnet
class CarnetSystem {
    constructor() {
        this.currentTemplate = null;
        this.currentMiembro = null;
        this.personalizacion = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadTemplates();
        this.initializeColorPickers();
        this.initializeQRGenerator();
        this.initializePDFGenerator();
    }

    bindEvents() {
        // Eventos de selección de plantilla
        document.addEventListener('click', (e) => {
            if (e.target.closest('.template-card')) {
                this.selectTemplate(e.target.closest('.template-card'));
            }
        });

        // Eventos de personalización
        document.addEventListener('change', (e) => {
            if (e.target.matches('.carnet-color-input input[type="color"]')) {
                this.updateColors();
            }
            if (e.target.matches('.carnet-typography-controls input, .carnet-typography-controls select')) {
                this.updateTypography();
            }
            if (e.target.matches('.carnet-data-controls input, .carnet-data-controls textarea')) {
                this.updateData();
            }
        });

        // Eventos de paleta de colores
        document.addEventListener('click', (e) => {
            if (e.target.closest('.carnet-color-option')) {
                this.selectColorPalette(e.target.closest('.carnet-color-option'));
            }
        });

        // Eventos de botones de acción
        document.addEventListener('click', (e) => {
            if (e.target.matches('.carnet-btn-primary')) {
                this.savePersonalization();
            }
            if (e.target.matches('.carnet-btn-success')) {
                this.generateCarnet();
            }
            if (e.target.matches('.carnet-pdf-btn-primary')) {
                this.generatePDF();
            }
        });
    }

    loadTemplates() {
        // Cargar plantillas disponibles
        fetch('/api/carnet/templates')
            .then(response => response.json())
            .then(templates => {
                this.renderTemplates(templates);
            })
            .catch(error => {
                console.error('Error cargando plantillas:', error);
            });
    }

    renderTemplates(templates) {
        const container = document.querySelector('.carnet-templates-container');
        if (!container) return;

        container.innerHTML = templates.map(template => `
            <div class="template-card" data-template-id="${template.id}">
                <div class="template-preview">
                    <div class="carnet-preview ${template.template_path.replace('.', '-')}">
                        <!-- Vista previa de la plantilla -->
                    </div>
                </div>
                <div class="template-info">
                    <h5>${template.nombre}</h5>
                    <p>${template.descripcion}</p>
                    <button class="btn btn-primary btn-sm">Seleccionar</button>
                </div>
            </div>
        `).join('');
    }

    selectTemplate(templateCard) {
        // Remover selección anterior
        document.querySelectorAll('.template-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Seleccionar nueva plantilla
        templateCard.classList.add('selected');
        const templateId = templateCard.dataset.templateId;
        
        this.currentTemplate = templateId;
        this.loadTemplateData(templateId);
    }

    loadTemplateData(templateId) {
        fetch(`/api/carnet/templates/${templateId}`)
            .then(response => response.json())
            .then(template => {
                this.renderTemplatePreview(template);
                this.loadPersonalization(templateId);
            })
            .catch(error => {
                console.error('Error cargando plantilla:', error);
            });
    }

    renderTemplatePreview(template) {
        const previewContainer = document.querySelector('.carnet-preview-container');
        if (!previewContainer) return;

        previewContainer.innerHTML = `
            <div class="carnet-base ${template.template_path.replace('.', '-')}" id="carnet-preview">
                <!-- Contenido del carnet se renderiza dinámicamente -->
            </div>
        `;

        this.updatePreview();
    }

    loadPersonalization(templateId) {
        const miembroId = document.querySelector('[data-miembro-id]')?.dataset.miembroId;
        if (!miembroId) return;

        fetch(`/api/carnet/personalizacion/${miembroId}/${templateId}`)
            .then(response => response.json())
            .then(personalizacion => {
                this.personalizacion = personalizacion;
                this.applyPersonalization();
            })
            .catch(error => {
                console.error('Error cargando personalización:', error);
            });
    }

    applyPersonalization() {
        if (!this.personalizacion) return;

        // Aplicar colores
        document.querySelectorAll('.carnet-color-input input[type="color"]').forEach(input => {
            const colorType = input.name;
            if (this.personalizacion[colorType]) {
                input.value = this.personalizacion[colorType];
            }
        });

        // Aplicar tipografía
        document.querySelectorAll('.carnet-typography-controls input, .carnet-typography-controls select').forEach(input => {
            const property = input.name;
            if (this.personalizacion[property] !== undefined) {
                input.value = this.personalizacion[property];
            }
        });

        // Aplicar datos personalizados
        if (this.personalizacion.datos_personalizados) {
            Object.entries(this.personalizacion.datos_personalizados).forEach(([key, value]) => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = value;
                }
            });
        }

        this.updatePreview();
    }

    updateColors() {
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

    updateTypography() {
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

    updateData() {
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

    updatePreview() {
        this.updateColors();
        this.updateTypography();
        this.updateData();
        this.generateQR();
    }

    selectColorPalette(colorOption) {
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

        this.updatePreview();
    }

    initializeColorPickers() {
        // Inicializar paleta de colores predefinidos
        const colorPalette = document.querySelector('.carnet-color-palette');
        if (colorPalette) {
            colorPalette.innerHTML = `
                <div class="carnet-color-option" data-primary="#4facfe" data-secondary="#00f2fe">
                    <div class="carnet-color-preview carnet-palette-azul"></div>
                    <span class="carnet-color-name">Azul-Cian</span>
                </div>
                <div class="carnet-color-option" data-primary="#43e97b" data-secondary="#38f9d7">
                    <div class="carnet-color-preview carnet-palette-verde"></div>
                    <span class="carnet-color-name">Verde</span>
                </div>
                <div class="carnet-color-option" data-primary="#fa709a" data-secondary="#fee140">
                    <div class="carnet-color-preview carnet-palette-rosa"></div>
                    <span class="carnet-color-name">Rosa-Amarillo</span>
                </div>
                <div class="carnet-color-option" data-primary="#667eea" data-secondary="#764ba2">
                    <div class="carnet-color-preview carnet-palette-purple"></div>
                    <span class="carnet-color-name">Púrpura</span>
                </div>
                <div class="carnet-color-option" data-primary="#f093fb" data-secondary="#f5576c">
                    <div class="carnet-color-preview carnet-palette-orange"></div>
                    <span class="carnet-color-name">Rosa-Rojo</span>
                </div>
                <div class="carnet-color-option" data-primary="#008080" data-secondary="#00ced1">
                    <div class="carnet-color-preview carnet-palette-teal"></div>
                    <span class="carnet-color-name">Teal</span>
                </div>
            `;
        }
    }

    initializeQRGenerator() {
        // Configurar generador de QR
        this.qrConfig = {
            width: 120,
            height: 120,
            color: {
                dark: '#000000',
                light: '#ffffff'
            },
            errorCorrectionLevel: 'M'
        };
    }

    generateQR() {
        const qrContainer = document.querySelector('#carnet-qr-code');
        if (!qrContainer) return;

        const qrData = this.getQRData();
        
        // Limpiar contenedor
        qrContainer.innerHTML = '';
        
        QRCode.toCanvas(qrContainer, qrData, this.qrConfig)
            .then(() => {
                console.log('QR Code generado exitosamente');
            })
            .catch(error => {
                console.error('Error generando QR:', error);
                qrContainer.innerHTML = '<div style="text-align: center; color: #666; font-size: 10px;">QR no disponible</div>';
            });
    }

    getQRData() {
        const miembroId = document.querySelector('[data-miembro-id]')?.dataset.miembroId;
        const numeroCarnet = document.querySelector('input[name="numero_carnet"]')?.value;
        
        return JSON.stringify({
            miembro_id: miembroId,
            numero_carnet: numeroCarnet,
            url: `${window.location.origin}/miembros/${miembroId}`,
            timestamp: new Date().toISOString()
        });
    }

    initializePDFGenerator() {
        // Configurar generador de PDF
        this.pdfConfig = {
            format: 'a4',
            orientation: 'portrait',
            quality: 'high'
        };
    }

    generatePDF() {
        const carnetElement = document.querySelector('#carnet-preview');
        if (!carnetElement) {
            this.showNotification('Error: No se encontró el elemento del carnet', 'error');
            return;
        }

        // Mostrar progreso
        this.showPDFProgress();

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
            const fileName = `carnet-${window.carnetMiembro?.id || 'miembro'}-${Date.now()}.pdf`;
            pdf.save(fileName);

            this.hidePDFProgress();
            this.showNotification('PDF generado exitosamente', 'success');
        }).catch(error => {
            console.error('Error generando PDF:', error);
            this.hidePDFProgress();
            this.showNotification('Error generando PDF. Intenta nuevamente.', 'error');
        });
    }

    showPDFProgress() {
        const progress = document.querySelector('.carnet-pdf-progress');
        if (progress) {
            progress.classList.add('show');
        }
    }

    hidePDFProgress() {
        const progress = document.querySelector('.carnet-pdf-progress');
        if (progress) {
            progress.classList.remove('show');
        }
    }

    savePersonalization() {
        const miembroId = window.carnetMiembro?.id;
        if (!miembroId) {
            this.showNotification('Error: No se encontró el ID del miembro', 'error');
            return;
        }

        const data = {
            template_id: this.currentTemplate,
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
                this.showNotification('Personalización guardada correctamente', 'success');
            } else {
                this.showNotification('Error guardando personalización', 'error');
            }
        })
        .catch(error => {
            console.error('Error guardando personalización:', error);
            this.showNotification('Error guardando personalización', 'error');
        });
    }

    generateCarnet() {
        const miembroId = window.carnetMiembro?.id;
        const templateId = this.currentTemplate;

        if (!miembroId || !templateId) {
            this.showNotification('Selecciona una plantilla primero', 'warning');
            return;
        }

        // Abrir carnet en nueva ventana
        const url = `/carnet/${miembroId}/generar/${templateId}`;
        window.open(url, '_blank');
    }

    showNotification(message, type = 'info') {
        // Implementar sistema de notificaciones
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}

// Inicializar sistema cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.carnetSystem = new CarnetSystem();
});

// Exportar para uso en otros módulos
export default CarnetSystem;
