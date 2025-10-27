/* ==========================================
   MIEMBROS PROFILE - JAVASCRIPT OPTIMIZADO
   ========================================== */

// Variables globales
let carnetModal = null;
let carnetData = null;

// Función para abrir el modal del carnet
function abrirCarnetModal(miembroId) {
    console.log('Abriendo carnet para miembro:', miembroId);
    
    // Simular datos del carnet (en producción vendría del servidor)
    carnetData = {
        id: miembroId,
        nombre: 'Juan Pérez',
        apellido: 'García',
        profesion: 'Ingeniero',
        organizacion: 'CLDCI',
        numero_carnet: 'CLDCI-2024-001',
        estado: 'activa',
        foto_url: null
    };
    
    // Actualizar el contenido del modal
    document.getElementById('carnet-miembro-nombre').textContent = carnetData.nombre + ' ' + carnetData.apellido;
    document.getElementById('carnet-numero').textContent = carnetData.numero_carnet;
    document.getElementById('carnet-profesion').textContent = carnetData.profesion;
    document.getElementById('carnet-organizacion').textContent = carnetData.organizacion;
    
    // Actualizar estado
    const estadoElement = document.getElementById('carnet-estado');
    estadoElement.textContent = carnetData.estado.toUpperCase();
    estadoElement.className = `carnet-status ${carnetData.estado}`;
    
    // Mostrar el modal
    carnetModal = document.getElementById('carnetModal');
    if (carnetModal) {
        carnetModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Generar QR
        generarQR();
    }
}

// Función para cerrar el modal del carnet
function cerrarCarnetModal() {
    if (carnetModal) {
        carnetModal.style.animation = 'modalSlideOut 0.3s ease-in';
        setTimeout(() => {
            carnetModal.style.display = 'none';
            carnetModal.style.animation = '';
            document.body.style.overflow = '';
        }, 300);
    }
}

// Función para generar el código QR
function generarQR() {
    const qrContainer = document.getElementById('carnet-qr-code');
    if (qrContainer && carnetData) {
        // Limpiar contenido anterior
        qrContainer.innerHTML = '';
        
        // Crear elemento QR
        const qrElement = document.createElement('div');
        qrElement.id = 'qr-code';
        qrElement.style.width = '120px';
        qrElement.style.height = '120px';
        qrElement.style.margin = '0 auto';
        
        qrContainer.appendChild(qrElement);
        
        // Generar QR con la información del carnet
        const qrData = JSON.stringify({
            id: carnetData.id,
            numero: carnetData.numero_carnet,
            nombre: carnetData.nombre + ' ' + carnetData.apellido,
            organizacion: carnetData.organizacion,
            fecha: new Date().toISOString()
        });
        
        // Usar QRCode.js para generar el código
        if (typeof QRCode !== 'undefined') {
            new QRCode(qrElement, {
                text: qrData,
                width: 120,
                height: 120,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        } else {
            // Fallback si QRCode no está disponible
            qrElement.innerHTML = `
                <div style="width: 120px; height: 120px; background: #f8f9fa; border: 2px dashed #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.75rem; text-align: center;">
                    QR Code<br>No disponible
                </div>
            `;
        }
    }
}

// Función para imprimir el carnet
function imprimirCarnet() {
    if (carnetModal) {
        const printContent = carnetModal.querySelector('.carnet-modal-content');
        if (printContent) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Carnet Digital - ${carnetData.nombre} ${carnetData.apellido}</title>
                        <style>
                            body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                            .carnet-print { 
                                width: 85mm; 
                                height: 54mm; 
                                border: 1px solid #000; 
                                padding: 10px; 
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                position: relative;
                                overflow: hidden;
                            }
                            .carnet-print::before {
                                content: '';
                                position: absolute;
                                top: -50%;
                                right: -50%;
                                width: 200%;
                                height: 200%;
                                background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
                                animation: rotate 20s linear infinite;
                                z-index: 1;
                            }
                            @keyframes rotate {
                                from { transform: rotate(0deg); }
                                to { transform: rotate(360deg); }
                            }
                            .carnet-header { text-align: center; margin-bottom: 10px; position: relative; z-index: 2; }
                            .carnet-logo { width: 30px; height: 30px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; }
                            .carnet-photo { width: 40px; height: 40px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.4); object-fit: cover; }
                            .carnet-info { background: rgba(255,255,255,0.15); border-radius: 5px; padding: 5px; margin: 5px 0; backdrop-filter: blur(10px); }
                            .carnet-name { font-size: 12px; font-weight: 700; margin: 2px 0; }
                            .carnet-profession { font-size: 10px; opacity: 0.9; }
                            .carnet-org { font-size: 9px; opacity: 0.8; margin-top: 2px; }
                            .carnet-number { font-size: 10px; font-weight: bold; letter-spacing: 1px; }
                            .carnet-status { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 8px; font-weight: 600; text-transform: uppercase; }
                            .carnet-status.activa { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
                        </style>
                    </head>
                    <body>
                        <div class="carnet-print">
                            <div class="carnet-header">
                                <div class="carnet-logo">CLDCI</div>
                                <img src="${carnetData.foto_url || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNmOGY5ZmEiLz4KPHBhdGggZD0iTTIwIDIwQzIyLjIwOTEgMjAgMjQgMTguMjA5MSAyNCAxNkMyNCAxMy43OTA5IDIyLjIwOTEgMTIgMjAgMTJDMTcuNzkwOSAxMiAxNiAxMy43OTA5IDE2IDE2QzE2IDE4LjIwOTEgMTcuNzkwOSAyMCAyMCAyMFoiIGZpbGw9IiM2Yzc1N2QiLz4KPHBhdGggZD0iTTIwIDI0QzE2LjY4NjMgMjQgMTMuNTY1MiAyMi4yMDkxIDEyIDIwQzEzLjU2NTIgMTcuNzkwOSAxNi42ODYzIDE2IDIwIDE2QzIzLjMxMzcgMTYgMjYuNDM0OCAxNy43OTA5IDI4IDIwQzI2LjQzNDggMjIuMjA5MSAyMy4zMTM3IDI0IDIwIDI0WiIgZmlsbD0iIzZjNzU3ZCIvPgo8L3N2Zz4K'}" 
                                     alt="${carnetData.nombre}" class="carnet-photo">
                            </div>
                            <div class="carnet-info">
                                <div class="carnet-name">${carnetData.nombre} ${carnetData.apellido}</div>
                                <div class="carnet-profession">${carnetData.profesion}</div>
                                <div class="carnet-org">${carnetData.organizacion}</div>
                                <div class="carnet-number">${carnetData.numero_carnet}</div>
                                <div class="carnet-status ${carnetData.estado}">${carnetData.estado.toUpperCase()}</div>
                            </div>
                        </div>
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    }
}

// Función para descargar el carnet como PDF
function descargarCarnetPDF() {
    if (carnetModal && typeof html2canvas !== 'undefined' && typeof jsPDF !== 'undefined') {
        const carnetContent = carnetModal.querySelector('.carnet-modal-content');
        
        html2canvas(carnetContent, {
            backgroundColor: null,
            scale: 2,
            useCORS: true,
            allowTaint: true
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: [85, 54]
            });
            
            const imgWidth = 85;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
            pdf.save('carnet-' + document.getElementById('carnet-numero').textContent + '.pdf');
        }).catch(error => {
            console.error('Error generando PDF:', error);
            alert('Error al generar el PDF. Intenta nuevamente.');
        });
    }
}

// Función para compartir el carnet
function compartirCarnet() {
    if (navigator.share) {
        navigator.share({
            title: 'Carnet Digital - ' + document.getElementById('carnet-miembro-nombre').textContent,
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

// Función para copiar enlace
function copiarEnlace() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        // Mostrar toast
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

// Navegación lateral mejorada
function initProfileNavigation() {
    const navLinks = document.querySelectorAll('.profile-nav .nav-link');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remover clase active de todos los links
            navLinks.forEach(navLink => navLink.classList.remove('active'));
            
            // Agregar clase active al link clickeado
            this.classList.add('active');
            
            // Obtener el target del tab
            const targetId = this.getAttribute('href');
            const targetPane = document.querySelector(targetId);
            
            if (targetPane) {
                // Ocultar todos los panes
                tabPanes.forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                
                // Mostrar el pane seleccionado
                targetPane.classList.add('show', 'active');
                
                // Scroll suave al contenido
                targetPane.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        });
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar navegación lateral
    initProfileNavigation();
    // Event listener para enlaces del carnet
    document.addEventListener('click', function(e) {
        if (e.target.closest('.carnet-link')) {
            e.preventDefault();
            e.stopPropagation();
            const miembroId = e.target.closest('.carnet-link').getAttribute('data-miembro-id');
            console.log('Clic en carnet para miembro:', miembroId);
            abrirCarnetModal(miembroId);
        }
    });

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarCarnetModal();
        }
    });

    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById('carnetModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarCarnetModal();
            }
        });
    }
});
