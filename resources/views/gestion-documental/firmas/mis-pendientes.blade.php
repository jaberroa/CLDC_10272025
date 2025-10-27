@extends('partials.layouts.master')

@section('title', 'Mis Firmas Pendientes | CLDCI')
@section('pagetitle', 'Mis Firmas Pendientes')

@section('content')
<!-- Botón Volver -->
<div class="mb-3">
    <a href="{{ route('gestion-documental.dashboard') }}" class="btn btn-soft-secondary">
        <i class="ri-arrow-left-line me-1"></i>
        Volver al Dashboard
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-quill-pen-line me-2"></i>
                    Documentos Pendientes de Firma
                </h5>
            </div>
            <div class="card-body">
                @forelse($firmantes as $firmante)
                <div class="border rounded p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1">
                                <i class="ri-file-text-line me-2"></i>
                                {{ $firmante->solicitudFirma->documento->titulo }}
                            </h6>
                            <p class="text-muted mb-1 small">
                                <i class="ri-folder-line me-1"></i>
                                {{ $firmante->solicitudFirma->documento->seccion->nombre }} / 
                                {{ $firmante->solicitudFirma->documento->carpeta->nombre }}
                            </p>
                            <p class="text-muted mb-0 small">
                                <i class="ri-user-line me-1"></i>
                                Solicitado por: {{ $firmante->solicitudFirma->solicitadoPor->name }}
                            </p>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="badge bg-warning mb-2">
                                <i class="ri-time-line me-1"></i>
                                Pendiente
                            </span>
                            <p class="text-muted small mb-0">
                                Orden: {{ $firmante->orden }} de {{ $firmante->solicitudFirma->firmantes->count() }}
                            </p>
                            <p class="text-muted small mb-0">
                                Solicitado {{ $firmante->solicitudFirma->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="{{ route('gestion-documental.documentos.show', $firmante->solicitudFirma->documento) }}" 
                               class="btn btn-sm btn-soft-info mb-2">
                                <i class="ri-eye-line me-1"></i> 
                                Ver Documento
                            </a>
                            <button class="btn btn-sm btn-success" onclick="firmarDocumento({{ $firmante->id }})">
                                <i class="ri-quill-pen-line me-1"></i> 
                                Firmar Ahora
                            </button>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    @if($firmante->solicitudFirma->mensaje)
                    <div class="mt-3 pt-3 border-top">
                        <p class="mb-0 small">
                            <strong>Mensaje:</strong> {{ $firmante->solicitudFirma->mensaje }}
                        </p>
                    </div>
                    @endif
                    
                    <!-- Progreso de firmas -->
                    <div class="mt-3">
                        <div class="d-flex align-items-center small text-muted">
                            <i class="ri-progress-3-line me-2"></i>
                            <strong>Progreso de firmas:</strong>
                            <div class="progress flex-grow-1 mx-2" style="height: 8px;">
                                @php
                                    $totalFirmantes = $firmante->solicitudFirma->firmantes->count();
                                    $firmados = $firmante->solicitudFirma->firmantes->where('estado', 'firmado')->count();
                                    $porcentaje = $totalFirmantes > 0 ? ($firmados / $totalFirmantes) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <span>{{ $firmados }}/{{ $totalFirmantes }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="ri-quill-pen-line display-1 text-muted"></i>
                    <h5 class="mt-3">No tienes firmas pendientes</h5>
                    <p class="text-muted">Todos los documentos están firmados o no requieren tu firma</p>
                </div>
                @endforelse
                
                @if($firmantes->isNotEmpty())
                <div class="mt-3">
                    {{ $firmantes->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Firma Electrónica -->
<div class="modal fade" id="firmaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-quill-pen-line me-2"></i>
                    Firma Electrónica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    Tu firma electrónica quedará registrada con fecha, hora, IP y datos de autenticación.
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Comentarios (opcional)</label>
                    <textarea class="form-control" id="firmaComentarios" rows="3" 
                              placeholder="Agrega comentarios sobre tu firma..."></textarea>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="firmaAceptacion" required>
                    <label class="form-check-label" for="firmaAceptacion">
                        Confirmo que he revisado el documento y acepto firmarlo electrónicamente
                    </label>
                </div>
                
                <div class="border-top pt-3">
                    <p class="small text-muted mb-0">
                        <i class="ri-shield-check-line me-1"></i>
                        <strong>Información de la firma:</strong>
                    </p>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li><i class="ri-user-line me-1"></i> Firmante: {{ auth()->user()->name }}</li>
                        <li><i class="ri-calendar-line me-1"></i> Fecha: {{ now()->format('d/m/Y H:i:s') }}</li>
                        <li><i class="ri-global-line me-1"></i> IP: {{ request()->ip() }}</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnConfirmarFirma" disabled>
                    <i class="ri-quill-pen-line me-1"></i>
                    Confirmar Firma
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let firmanteIdActual = null;
    
    function firmarDocumento(firmanteId) {
        firmanteIdActual = firmanteId;
        const modal = new bootstrap.Modal(document.getElementById('firmaModal'));
        modal.show();
    }
    
    // Habilitar botón solo si acepta términos
    document.getElementById('firmaAceptacion')?.addEventListener('change', function() {
        document.getElementById('btnConfirmarFirma').disabled = !this.checked;
    });
    
    // Confirmar firma
    document.getElementById('btnConfirmarFirma')?.addEventListener('click', function() {
        if (!firmanteIdActual) return;
        
        const comentarios = document.getElementById('firmaComentarios').value;
        const btn = this;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Firmando...';
        
        fetch(`/gestion-documental/firmas/${firmanteIdActual}/firmar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                comentarios: comentarios,
                ip_firma: '{{ request()->ip() }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessToast('Documento firmado exitosamente');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showErrorToast(data.message || 'Error al firmar el documento');
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-quill-pen-line me-1"></i> Confirmar Firma';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Error al procesar la firma');
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-quill-pen-line me-1"></i> Confirmar Firma';
        });
    });
    
    // Resetear modal al cerrar
    document.getElementById('firmaModal')?.addEventListener('hidden.bs.modal', function() {
        document.getElementById('firmaComentarios').value = '';
        document.getElementById('firmaAceptacion').checked = false;
        document.getElementById('btnConfirmarFirma').disabled = true;
        firmanteIdActual = null;
    });
</script>
@endsection

