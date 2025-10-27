<!-- Modal del Carnet Digital -->
<div class="carnet-modal" id="carnetModal" style="display: none;">
    <div class="carnet-modal-content">
        <!-- Header del Modal -->
        <div class="carnet-modal-header">
            <h3 class="carnet-modal-title">
                <i class="ri-qr-code-line me-2"></i>
                Carnet Digital - <span id="carnet-miembro-nombre"></span>
            </h3>
            <button class="carnet-modal-close" onclick="cerrarCarnetModal()">
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
                    <div id="carnet-foto-container">
                        <!-- Se carga dinámicamente -->
                    </div>
                </div>

                <!-- Información del Miembro -->
                <div class="carnet-info">
                    <div class="text-center">
                        <div class="carnet-name" id="carnet-nombre"></div>
                        <div class="carnet-profession" id="carnet-profesion"></div>
                        <div class="carnet-org" id="carnet-organizacion"></div>
                        <div class="mt-2">
                            <span class="carnet-status activa">Activa</span>
                        </div>
                    </div>
                </div>

                <!-- Número de Carnet -->
                <div class="text-center mb-3">
                    <div style="font-size: 0.7rem; opacity: 0.8; margin-bottom: 0.25rem;">Número de Carnet</div>
                    <div class="carnet-number" id="carnet-numero"></div>
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
                            <div style="font-size: 0.8rem; font-weight: 600;" id="carnet-tipo-membresia"></div>
                        </div>
                        <div class="col-6">
                            <div style="font-size: 0.6rem; opacity: 0.8; margin-bottom: 0.25rem;">Miembro Desde</div>
                            <div style="font-size: 0.8rem; font-weight: 600;" id="carnet-fecha-ingreso"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="carnet-footer">
                    <div style="font-weight: 600; margin-bottom: 0.25rem;">www.cldci.org.do</div>
                    <div id="carnet-valido-hasta"></div>
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
            <button onclick="cerrarCarnetModal()" class="btn btn-outline-secondary">
                <i class="ri-close-line me-2"></i> Cerrar
            </button>
        </div>
    </div>
</div>


