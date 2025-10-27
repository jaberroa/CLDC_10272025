{{-- Ejemplos de uso del Toast Global --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ejemplos de Toast Global</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-success" onclick="showSuccessToast('Operación completada exitosamente')">
                        <i class="ri-check-line me-1"></i>
                        Toast de Éxito
                    </button>
                    
                    <button type="button" class="btn btn-info" onclick="showInfoToast('Información importante para el usuario')">
                        <i class="ri-information-line me-1"></i>
                        Toast de Información
                    </button>
                    
                    <button type="button" class="btn btn-warning" onclick="showWarningToast('Advertencia: Revisar datos ingresados')">
                        <i class="ri-alert-line me-1"></i>
                        Toast de Advertencia
                    </button>
                    
                    <button type="button" class="btn btn-danger" onclick="showErrorToast('Error al procesar la solicitud')">
                        <i class="ri-error-warning-line me-1"></i>
                        Toast de Error
                    </button>
                    
                    <button type="button" class="btn btn-primary" onclick="showToast('Mensaje personalizado', 'success', 'Título Personalizado')">
                        <i class="ri-notification-line me-1"></i>
                        Toast Personalizado
                    </button>
                </div>
                
                <hr>
                
                <div class="alert alert-info">
                    <h6><i class="ri-information-line me-2"></i>Características del Toast Global:</h6>
                    <ul class="mb-0">
                        <li><strong>Transición de 3 segundos:</strong> El toast se muestra durante 3 segundos completos</li>
                        <li><strong>Desvanecimiento:</strong> Efecto de desvanecimiento de 3 segundos adicionales</li>
                        <li><strong>Diseño coherente:</strong> Mismo diseño en todas las vistas del proyecto</li>
                        <li><strong>Responsive:</strong> Se adapta a dispositivos móviles</li>
                        <li><strong>Múltiples tipos:</strong> Success, Info, Warning, Error</li>
                        <li><strong>Hora actual:</strong> Muestra la hora exacta de la notificación</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="ri-code-line me-2"></i>Uso en JavaScript:</h6>
                    <pre class="mb-0"><code>// Toast básico de éxito
showSuccessToast('Mensaje de éxito');

// Toast de información
showInfoToast('Mensaje informativo');

// Toast de advertencia
showWarningToast('Mensaje de advertencia');

// Toast de error
showErrorToast('Mensaje de error');

// Toast personalizado
showToast('Mensaje', 'success', 'Título personalizado');</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>

